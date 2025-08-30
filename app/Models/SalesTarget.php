<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id','period','start_date','end_date','target_amount'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function staff(): BelongsTo { return $this->belongsTo(User::class, 'staff_id'); }
}

