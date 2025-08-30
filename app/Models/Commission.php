<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id','staff_id','rate','amount','type'
    ];

    public function payment(): BelongsTo { return $this->belongsTo(Payment::class); }
    public function staff(): BelongsTo { return $this->belongsTo(User::class, 'staff_id'); }
}

