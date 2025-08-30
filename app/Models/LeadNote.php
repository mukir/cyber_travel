<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id','sales_rep_id','content','next_follow_up'
    ];

    protected $casts = [
        'next_follow_up' => 'date',
    ];

    public function lead(): BelongsTo { return $this->belongsTo(Lead::class); }
    public function salesRep(): BelongsTo { return $this->belongsTo(User::class, 'sales_rep_id'); }
}

