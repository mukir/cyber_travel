<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id', 'method', 'amount', 'status', 'reference', 'receipt_number', 'provider_payload',
    ];

    protected $casts = [
        'provider_payload' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    // Automatically create a commission record when a payment is marked as paid
    protected static function booted(): void
    {
        $createCommission = function (self $payment): void {
            try {
                if ($payment->status !== 'paid') {
                    return;
                }
                // Avoid duplicates per payment
                if (\App\Models\Commission::where('payment_id', $payment->id)->exists()) {
                    return;
                }
                $booking = $payment->booking;
                if (!$booking || !$booking->referred_by_id) {
                    return; // no referring staff to pay commission to
                }
                $rate = (float)config('sales.commission_rate', (float)env('SALES_COMMISSION_RATE', 10));
                $amount = round(((float)$payment->amount) * $rate / 100, 2);
                \App\Models\Commission::create([
                    'payment_id' => $payment->id,
                    'staff_id'   => $booking->referred_by_id,
                    'rate'       => $rate,
                    'amount'     => $amount,
                    'type'       => 'milestone',
                ]);
            } catch (\Throwable $e) {
                // fail-safe: never break payment flow
                \Log::error('Failed creating commission', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]);
            }
        };

        static::created(function (self $payment) use ($createCommission) {
            $createCommission($payment);
        });
        static::updated(function (self $payment) use ($createCommission) {
            // Only react when status moved to paid or duplicates don't exist
            $createCommission($payment);
        });
        static::saved(function (self $payment) use ($createCommission) {
            // For safety in case of different event orders
            $createCommission($payment);
        });
    }
}
