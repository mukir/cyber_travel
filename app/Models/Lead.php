<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_rep_id','client_id','name','email','phone','stage','status','next_follow_up','notes'
    ];

    protected $casts = [
        'next_follow_up' => 'date',
    ];

    public function salesRep(): BelongsTo { return $this->belongsTo(User::class, 'sales_rep_id'); }
    public function client(): BelongsTo { return $this->belongsTo(User::class, 'client_id'); }
    // Renamed to avoid collision with 'notes' text attribute on the leads table
    public function leadNotes(): HasMany { return $this->hasMany(LeadNote::class); }
}
