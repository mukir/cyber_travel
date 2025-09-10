<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\ClientDocument;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $q = Payment::with(['booking' => function ($b) {
            $b->with(['user', 'job', 'package']);
        }])->orderByDesc('created_at');

        if ($method = $request->get('method')) {
            $q->where('method', $method);
        }
        if ($status = $request->get('status')) {
            $q->where('status', $status);
        }
        if ($from = $request->get('from')) {
            $q->whereDate('created_at', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $q->whereDate('created_at', '<=', $to);
        }

        $payments = $q->paginate(20)->appends($request->query());

        // Simple stats
        $today = Payment::whereDate('created_at', today())->sum('amount');
        $month = Payment::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->sum('amount');

        return view('admin.payments', compact('payments', 'today', 'month'));
    }

    public function overdue(Request $request)
    {
        $q = Booking::with(['user', 'job', 'package'])
            ->whereColumn('amount_paid', '<', 'total_amount');

        // Heuristic: due date = start_date if set, else created_at + 7 days
        $q->where(function ($w) {
            $w->whereNotNull('start_date')->whereDate('start_date', '<', today())
              ->orWhere(function ($x) { $x->whereNull('start_date')->whereDate('created_at', '<', now()->subDays(7)); });
        });

        $overdue = $q->orderBy('start_date')->paginate(20);

        return view('admin.payments_overdue', compact('overdue'));
    }

    public function sendReminders(Request $request)
    {
        $data = $request->validate([
            'booking_ids' => ['required', 'array'],
            'booking_ids.*' => ['integer', 'exists:bookings,id'],
        ]);

        $bookings = Booking::whereIn('id', $data['booking_ids'])->get();

        foreach ($bookings as $b) {
            if ($b->customer_email) {
                // Very simple reminder email; replace with Mailable as needed
                Mail::raw(
                    'Dear '.$b->customer_name.",\nThis is a reminder to complete payment for booking #".$b->id.
                    ". Balance: ".number_format(max($b->total_amount - $b->amount_paid, 0), 2).' '.$b->currency,
                    function ($m) use ($b) {
                        $m->to($b->customer_email)->subject('Payment Reminder for Booking #'.$b->id);
                    }
                );
            }
        }

        return back()->with('success', 'Reminders sent to selected bookings.');
    }

    public function exportCsv(Request $request)
    {
        $q = Payment::with('booking');
        if ($method = $request->get('method')) { $q->where('method', $method); }
        if ($status = $request->get('status')) { $q->where('status', $status); }
        if ($from = $request->get('from')) { $q->whereDate('created_at', '>=', $from); }
        if ($to = $request->get('to')) { $q->whereDate('created_at', '<=', $to); }

        $rows = $q->orderBy('created_at')->get();

        $out = fopen('php://temp', 'w+');
        fputcsv($out, ['Date','Booking','Client','Method','Status','Amount','Outstanding','Reference']);
        foreach ($rows as $p) {
            $outstanding = $p->booking ? max(((float)$p->booking->total_amount) - ((float)$p->booking->amount_paid), 0) : 0;
            fputcsv($out, [
                $p->created_at->format('Y-m-d H:i'),
                'BK'.$p->booking_id,
                optional($p->booking)->customer_name,
                $p->method,
                $p->status,
                number_format($p->amount, 2),
                number_format($outstanding, 2),
                $p->receipt_number ?: $p->reference,
            ]);
        }
        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="payments.csv"',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $q = Payment::with(['booking' => function ($b) { $b->with('user'); }]);
        if ($method = $request->get('method')) { $q->where('method', $method); }
        if ($status = $request->get('status')) { $q->where('status', $status); }
        if ($from = $request->get('from')) { $q->whereDate('created_at', '>=', $from); }
        if ($to = $request->get('to')) { $q->whereDate('created_at', '<=', $to); }
        $rows = $q->orderBy('created_at')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.payments', [
            'rows' => $rows,
        ]);
        return $pdf->download('payments.pdf');
    }

    // Show manual payment form for a booking
    public function manualPaymentForm(Booking $booking)
    {
        $remaining = max(((float)$booking->total_amount) - ((float)$booking->amount_paid), 0);
        return view('admin.manual_payment', [
            'booking' => $booking->load(['user','job','package','payments']),
            'remaining' => $remaining,
        ]);
    }

    // Record a manual payment and update booking totals
    public function manualPayment(Request $request, Booking $booking)
    {
        // Prevent double full-payment
        $remaining = max(((float)$booking->total_amount) - ((float)$booking->amount_paid), 0);
        if ($remaining < 0.01) {
            return back()->with('error', 'This booking is already fully paid.');
        }

        $data = $request->validate([
            'method' => ['required', 'in:cash,bank,manual'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'reference' => ['nullable', 'string', 'max:255'],
            'receipt_number' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = isset($data['amount']) ? (float)$data['amount'] : 0.0;
        if ($amount <= 0.0) {
            // Default to remaining if not provided
            $amount = $remaining;
        }
        if ($amount > $remaining + 0.01) {
            $amount = $remaining; // clamp to remaining
        }

        // Create payment as paid
        $payment = Payment::create([
            'booking_id' => $booking->id,
            'method' => $data['method'],
            'amount' => $amount,
            'status' => 'paid',
            'reference' => $data['reference'] ?? null,
            'receipt_number' => $data['receipt_number'] ?? null,
            'provider_payload' => null,
        ]);

        // Update booking aggregates
        $newAmountPaid = (float)$booking->amount_paid + $amount;
        $statusText = 'installments';
        if ($booking->payments()->where('status', 'paid')->count() === 0 && $newAmountPaid < (float)$booking->total_amount) {
            $statusText = 'deposit';
        }
        if ($newAmountPaid >= (float)$booking->total_amount - 0.005) {
            $statusText = 'full';
        }

        $updates = [
            'amount_paid' => min($newAmountPaid, (float)$booking->total_amount),
            'payment_status' => $statusText,
        ];
        if ($statusText === 'full') {
            $updates['status'] = 'paid';
            $updates['paid_at'] = $booking->paid_at ?: now();
        } else {
            if ($booking->status !== 'paid') {
                $updates['status'] = 'partially_paid';
            }
        }
        $booking->update($updates);

        return redirect()->route('admin.payments')->with('success', 'Manual payment recorded (BK'.$booking->id.', '.number_format($amount, 2).').');
    }
}
