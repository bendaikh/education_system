<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildhoodPayment extends Model
{
    protected $table = 'childhood_payments';

    protected $fillable = [
        'subscription_id',
        'amount',
        'paid_amount',
        'status',
        'payment_date',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'date',
        'status' => 'string'
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(ChildhoodSubscription::class, 'subscription_id');
    }
}
