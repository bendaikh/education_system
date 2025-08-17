<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EducationalSubject extends Model
{
    protected $fillable = [
        'name',
        'description',
        'duration',
        'price',
        'status'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'status' => 'string'
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(EducationalSupportSubscription::class);
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'educational_subject_teacher');
    }
}
