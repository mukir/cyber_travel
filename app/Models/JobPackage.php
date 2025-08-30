<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id', 'name', 'description', 'price', 'duration_days', 'is_default',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

