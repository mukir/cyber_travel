<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'job_id', 'job_package_id', 'quantity', 'start_date', 'total_amount', 'amount_paid', 'payment_status', 'invoice_number', 'currency', 'status', 'customer_name', 'customer_email', 'customer_phone', 'notes', 'mpesa_checkout_id', 'mpesa_merchant_request_id', 'mpesa_receipt', 'paid_at',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(JobPackage::class, 'job_package_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
