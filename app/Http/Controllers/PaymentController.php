<?php

namespace App\Http\Controllers;

use App\Helpers\SafaricomDarajaHelper;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    // Polling endpoint to verify an M-Pesa STK payment by CheckoutRequestID (reference)
    public function mpesaStatus(string $ref)
    {
        $payment = Payment::where('reference', $ref)
            ->whereHas('booking', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest('id')
            ->first();

        if (!$payment) {
            return response()->json(['status' => 'failed', 'message' => 'Reference not found'], 404);
        }

        // If already terminal
        if ($payment->status === 'paid' && (float)$payment->amount > 0) {
            return response()->json(['status' => 'success', 'message' => 'Payment confirmed.']);
        }
        if ($payment->status === 'failed') {
            $msg = data_get($payment->provider_payload, 'stk_query.ResultDesc')
                ?? data_get($payment->provider_payload, 'ResultDesc')
                ?? 'Failed.';
            return response()->json(['status' => 'failed', 'message' => $msg]);
        }

        // Validate via STK Query (CheckoutRequestID is our reference)
        $checkoutId = $ref;
        if ($checkoutId) {
            $q = SafaricomDarajaHelper::validateStkTransaction($checkoutId);

            if (($q['status'] ?? '') === 'success' && isset($q['resultCode'])) {
                $resultCode = (int) $q['resultCode'];

                if ($resultCode === 0) {
                    // Success – idempotently mark payment as paid and update booking totals
                    DB::transaction(function () use ($payment, $q) {
                        // Reload with lock for safety
                        $pay = Payment::whereKey($payment->id)->lockForUpdate()->first();
                        $booking = \App\Models\Booking::whereKey($pay->booking_id)->lockForUpdate()->first();

                        // Merge provider payload to keep both callback and query
                        $payload = is_array($pay->provider_payload) ? $pay->provider_payload : [];
                        $payload['stk_query'] = $q['data'] ?? $q;

                        if ($pay->status !== 'paid') {
                            $pay->status = 'paid';
                        }
                        $pay->provider_payload = $payload;
                        $pay->save();

                        // Update booking amounts idempotently: only add if this payment wasn't counted yet
                        // Heuristic: if no existing paid marker for this payment in amount_paid, add
                        // Safer approach: recompute from sum of paid payments
                        $sumPaid = (float) $booking->payments()->where('status', 'paid')->sum('amount');

                        $statusText = 'installments';
                        if ($booking->payments()->where('status', 'paid')->count() === 1 && $sumPaid < (float)$booking->total_amount) {
                            // First successful payment and not full amount
                            $statusText = 'deposit';
                        }
                        if ($sumPaid >= (float)$booking->total_amount) {
                            $statusText = 'full';
                        }

                        $booking->amount_paid = $sumPaid;
                        if ($statusText === 'full') {
                            $booking->status = 'paid';
                            $booking->paid_at = $booking->paid_at ?: now();
                        }
                        $booking->payment_status = $statusText;
                        $booking->save();
                    });

                    return response()->json(['status' => 'success', 'message' => 'Processed successfully (via query)']);
                }

                // Common failure codes
                $failedCodes = [1032, 2001, 1037, 1001, 1002, 1];
                if (in_array($resultCode, $failedCodes, true)) {
                    $payload = is_array($payment->provider_payload) ? $payment->provider_payload : [];
                    $payload['stk_query'] = $q['data'] ?? $q;
                    $payment->status = 'failed';
                    $payment->provider_payload = $payload;
                    $payment->save();

                    return response()->json(['status' => 'failed', 'message' => $q['message'] ?? 'Declined']);
                }
            }
        }

        return response()->json(['status' => 'pending']);
    }
    public function payBooking(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        if ($booking->status === 'paid') {
            return back()->with('success', 'This booking is already paid.');
        }

        $data = $request->validate([
            'phone' => ['required', 'string'],
            'amount' => ['nullable', 'numeric', 'min:1'],
        ]);

        // Ensure MSISDN in 2547XXXXXXXX format if passed as 07XXXXXXXX
        $phone = preg_replace('/\D+/', '', $data['phone']);
        if (Str::startsWith($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        } elseif (Str::startsWith($phone, '7')) {
            $phone = '254' . $phone;
        }

        $amount = (float)($data['amount'] ?? $booking->total_amount);
        if ($amount > (float)$booking->total_amount) {
            $amount = (float)$booking->total_amount;
        }

        $reference = 'BK' . $booking->id;
        $description = 'Booking #' . $booking->id . ' payment';

        $resp = SafaricomDarajaHelper::stkPushRequest($phone, $amount, $reference, $description);

        if (($resp['status'] ?? 'error') === 'success') {
            $data = $resp['data'] ?? [];
            $booking->update([
                'mpesa_checkout_id' => $data['CheckoutRequestID'] ?? null,
                'mpesa_merchant_request_id' => $data['MerchantRequestID'] ?? null,
            ]);

            // Create a pending payment record
            Payment::create([
                'booking_id' => $booking->id,
                'method' => 'mpesa',
                'amount' => $amount,
                'status' => 'pending',
                'reference' => $data['CheckoutRequestID'] ?? null,
                'provider_payload' => $data,
            ]);

            return back()->with('success', 'STK push sent. Enter PIN on your phone to complete payment.');
        }

        return back()->with('error', $resp['message'] ?? 'Failed to initiate payment.');
    }

    public function verifyBooking(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        if (!$booking->mpesa_checkout_id) {
            return back()->with('error', 'No payment to verify for this booking.');
        }

        $res = SafaricomDarajaHelper::validateStkTransaction($booking->mpesa_checkout_id);
        if (($res['status'] ?? 'error') === 'success') {
            $code = (string)($res['resultCode'] ?? '');
            if ($code === '0') {
                // Mark booking paid partially/fully
                $paidAmount = (float)optional($booking->payments()->where('reference', $booking->mpesa_checkout_id)->latest()->first())->amount ?: 0;
                $newAmountPaid = (float)$booking->amount_paid + $paidAmount;

                $statusText = 'installments';
                if ($booking->payments()->where('status', 'paid')->count() === 0 && $newAmountPaid < (float)$booking->total_amount) {
                    $statusText = 'deposit';
                }
                if ($newAmountPaid >= (float)$booking->total_amount) {
                    $statusText = 'full';
                }

                $booking->update([
                    'status' => $statusText === 'full' ? 'paid' : $booking->status,
                    'paid_at' => $statusText === 'full' ? now() : $booking->paid_at,
                    'amount_paid' => $newAmountPaid,
                    'payment_status' => $statusText,
                ]);

                // Update payment record
                $payment = $booking->payments()->where('reference', $booking->mpesa_checkout_id)->latest()->first();
                if ($payment) {
                    $payment->update([
                        'status' => 'paid',
                        'receipt_number' => $booking->mpesa_receipt,
                        'provider_payload' => $res['data'] ?? null,
                    ]);
                }
                return back()->with('success', 'Payment verified. Thank you!');
            }
        }
        return back()->with('error', $res['message'] ?? 'Verification failed.');
    }

    // Safaricom STK callback
    public function stkCallback(Request $request)
    {
        $payload = $request->json()->all();
        Log::info('STK Callback received', $payload);

        try {
            $body = $payload['Body'] ?? [];
            $stk = $body['stkCallback'] ?? [];
            $checkoutId = $stk['CheckoutRequestID'] ?? null;
            $resultCode = (string)($stk['ResultCode'] ?? '');
            $items = collect(($stk['CallbackMetadata']['Item'] ?? []))->keyBy('Name');
            $receipt = optional($items->get('MpesaReceiptNumber'))['Value'] ?? null;

            if ($checkoutId) {
                $booking = Booking::where('mpesa_checkout_id', $checkoutId)->first();
                if ($booking) {
                    if ($resultCode === '0') {
                        $paidAmount = optional($booking->payments()->where('reference', $checkoutId)->latest()->first())->amount ?: 0;
                        $newAmountPaid = (float)$booking->amount_paid + (float)$paidAmount;

                        $statusText = 'installments';
                        if ($booking->payments()->where('status', 'paid')->count() === 0 && $newAmountPaid < (float)$booking->total_amount) {
                            $statusText = 'deposit';
                        }
                        if ($newAmountPaid >= (float)$booking->total_amount) {
                            $statusText = 'full';
                        }

                        $booking->update([
                            'status' => $statusText === 'full' ? 'paid' : $booking->status,
                            'paid_at' => $statusText === 'full' ? now() : $booking->paid_at,
                            'mpesa_receipt' => $receipt,
                            'amount_paid' => $newAmountPaid,
                            'payment_status' => $statusText,
                        ]);

                        // Update payment record
                        $payment = $booking->payments()->where('reference', $checkoutId)->latest()->first();
                        if ($payment) {
                            $payment->update([
                                'status' => 'paid',
                                'receipt_number' => $receipt,
                                'provider_payload' => $stk,
                            ]);
                        }
                    } else {
                        // Keep status as is; optionally log failure reason
                        Log::warning('STK payment not successful', ['booking_id' => $booking->id, 'resultCode' => $resultCode]);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error handling STK callback', ['error' => $e->getMessage()]);
        }

        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Callback received']);
    }

    // Standalone checkout page for a booking
    public function checkout(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        $paypalClientId = env('PAYPAL_CLIENT_ID');
        $currency = $booking->currency ?: 'KES';
        return view('checkout.booking', compact('booking', 'paypalClientId', 'currency'));
    }

    // PayPal completion endpoint (after client-side capture). Optionally verifies via server if secret provided.
    public function paypalComplete(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        $data = $request->validate([
            'order_id' => ['required', 'string'],
            'amount' => ['required', 'numeric', 'min:1'],
            'details' => ['nullable', 'array'],
        ]);

        $orderId = $data['order_id'];
        $amount = (float)$data['amount'];

        // Optional server verification if secret present
        $secret = env('PAYPAL_CLIENT_SECRET');
        $base = env('PAYPAL_BASE_URL', 'https://api-m.sandbox.paypal.com');
        if ($secret) {
            try {
                $tokenResp = \Illuminate\Support\Facades\Http::asForm()->withBasicAuth(env('PAYPAL_CLIENT_ID'), $secret)
                    ->post($base.'/v1/oauth2/token', ['grant_type' => 'client_credentials'])
                    ->json();
                $access = $tokenResp['access_token'] ?? null;
                if ($access) {
                    $orderResp = \Illuminate\Support\Facades\Http::withToken($access)
                        ->get($base.'/v2/checkout/orders/'.$orderId)
                        ->json();
                    $status = $orderResp['status'] ?? null;
                    $purchaseAmount = (float)($orderResp['purchase_units'][0]['amount']['value'] ?? 0);
                    if ($status !== 'COMPLETED' || abs($purchaseAmount - $amount) > 0.01) {
                        return back()->with('error', 'PayPal verification failed.');
                    }
                }
            } catch (\Throwable $e) {
                Log::error('PayPal verify failed', ['error' => $e->getMessage()]);
                return back()->with('error', 'PayPal verification failed.');
            }
        }

        // Record payment
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'method' => 'paypal',
            'amount' => $amount,
            'status' => 'paid',
            'reference' => $orderId,
            'provider_payload' => $data['details'] ?? null,
        ]);

        $newAmountPaid = (float)$booking->amount_paid + $amount;
        $statusText = 'installments';
        if ($booking->payments()->where('status', 'paid')->count() === 0 && $newAmountPaid < (float)$booking->total_amount) {
            $statusText = 'deposit';
        }
        if ($newAmountPaid >= (float)$booking->total_amount) {
            $statusText = 'full';
        }

        $booking->update([
            'amount_paid' => $newAmountPaid,
            'payment_status' => $statusText,
            'status' => $statusText === 'full' ? 'paid' : $booking->status,
            'paid_at' => $statusText === 'full' ? now() : $booking->paid_at,
        ]);

        return back()->with('success', 'Payment recorded. Thank you!');
    }

    // Invoice PDF
    public function invoice(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        $number = $booking->invoice_number ?: ('INV-'.str_pad($booking->id, 6, '0', STR_PAD_LEFT));
        if (!$booking->invoice_number) {
            $booking->update(['invoice_number' => $number]);
        }
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', [
            'booking' => $booking->fresh(['job','package','payments']),
            'number' => $number,
        ]);
        return $pdf->download($number.'.pdf');
    }

    // Receipt PDF (for latest/combined payments)
    public function receipt(Booking $booking)
    {
        abort_unless($booking->user_id === Auth::id(), 403);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.receipt', [
            'booking' => $booking->fresh(['job','package','payments']),
        ]);
        $name = 'RECEIPT-BK'.$booking->id.'.pdf';
        return $pdf->download($name);
    }
}
