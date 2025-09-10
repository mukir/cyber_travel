<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayoutBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'month', 'total_amount', 'total_count', 'processed_by', 'emailed', 'emailed_at',
    ];
}

