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
                $booking = $payment->booking;
                if (!$booking || !$booking->referred_by_id) {
                    return; // no referring staff to pay commission to
                }
                // Only award region commission when booking is fully paid up
                $sumPaid = (float) $booking->payments()->where('status', 'paid')->sum('amount');
                $isPaidUp = (\App\Services\CommissionRules::isBookingPaidUp($booking)) || ($sumPaid + 0.01 >= (float)$booking->total_amount);

                // Determine if a commission for this booking already exists (region_fixed)
                $existsForBooking = \App\Models\Commission::where('type', 'region_fixed')
                    ->whereHas('payment', function($q) use ($booking) { $q->where('booking_id', $booking->id); })
                    ->exists();

                if ($isPaidUp && !$existsForBooking) {
                    $job = $booking->job;
                    $fixed = \App\Services\CommissionRules::computeRegionCommission($job);
                    if ($fixed !== null) {
                        // Link the commission to this (final/any) paid payment
                        \App\Models\Commission::create([
                            'payment_id' => $payment->id,
                            'staff_id'   => $booking->referred_by_id,
                            'rate'       => 0,
                            'amount'     => $fixed,
                            'type'       => 'region_fixed',
                        ]);
                        return; // done
                    }
                }

                // No percentage fallback: policy favors fixed commissions by region only.
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
