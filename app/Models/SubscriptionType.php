<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'formation_id',
        'description',
        'price',
        'duration',
        'features',
        'max_students',
        'is_active',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scope for active subscription types
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessor for formatted price
    public function getFormattedPriceAttribute()
    {
        if ($this->price == 0) {
            return 'Free';
        }
        return '$' . number_format($this->price, 2) . '/' . $this->duration;
    }

    // Accessor for student limit display
    public function getStudentLimitDisplayAttribute()
    {
        return $this->max_students ? $this->max_students . ' students' : 'Unlimited';
    }

    // Relationship to Formation
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }
}
