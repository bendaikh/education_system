<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Student;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'formation_id',
        'amount',
        'payment_type',
        'status',
        'due_date',
        'payment_date',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2'
    ];

    /**
     * Get the student that owns the payment
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the formation that owns the payment
     */
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Get the user who created the payment
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to get payments by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get overdue payments
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'Paid');
    }

    /**
     * Get formatted amount with currency
     */
    public function getFormattedAmountAttribute()
    {
        // Get currency from settings
        $currency = \App\Models\Setting::get('currency', 'USD ($)');

        // Extract currency symbol from setting (e.g., "MAD (DH)" -> "DH", "USD ($)" -> "$")
        if (preg_match('/\(([^)]+)\)/', $currency, $matches)) {
            $currencySymbol = $matches[1];
        } else {
            // Fallback to first 3 characters if no parentheses found
            $currencySymbol = substr($currency, 0, 3);
        }

        return $currencySymbol . ' ' . number_format($this->amount, 2);
    }
}