<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_type_id',
        'student_id',
        'started_at',
        'expires_at',
        'auto_status',
        'next_billing_date',
        'amount_paid',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'next_billing_date' => 'date',
        'amount_paid' => 'decimal:2',
        'metadata' => 'array',
    ];

    // Relationships
    public function subscriptionType()
    {
        return $this->belongsTo(SubscriptionType::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('auto_status', 'active')->where('expires_at', '>', Carbon::now());
    }

    public function scopeExpired($query)
    {
        return $query->where('auto_status', 'expired')->orWhere('expires_at', '<=', Carbon::now());
    }

    public function scopeCancelled($query)
    {
        return $query->where('auto_status', 'cancelled');
    }

    // Accessors
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount_paid, 2);
    }

    public function getIsActiveAttribute()
    {
        return $this->auto_status === 'active' && $this->expires_at && $this->expires_at->isFuture();
    }

    public function getDaysUntilExpiryAttribute()
    {
        return $this->expires_at ? $this->expires_at->diffInDays(Carbon::now()) : null;
    }

    public function getStatusAttribute()
    {
        // Auto-calculate status based on expiry date
        if ($this->auto_status === 'cancelled') {
            return 'cancelled';
        }
        
        if ($this->expires_at && $this->expires_at->isPast()) {
            return 'expired';
        }
        
        return 'active';
    }

    // Auto-calculate next billing date based on subscription type
    public function calculateNextBilling()
    {
        if (!$this->subscriptionType || $this->auto_status !== 'active') {
            return null;
        }

        $nextBilling = $this->subscriptionType->duration === 'monthly' 
            ? $this->started_at->addMonth()
            : $this->started_at->addYear();

        return $nextBilling;
    }

    // Auto-calculate expiry date based on subscription type
    public function calculateExpiryDate()
    {
        if (!$this->subscriptionType) {
            return null;
        }

        return $this->subscriptionType->duration === 'monthly' 
            ? $this->started_at->addMonth()
            : $this->started_at->addYear();
    }
}
