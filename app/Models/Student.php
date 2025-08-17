<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'name',
        'email',
        'phone',
        'parent_phone',
        'address',
        'date_of_birth',
        'notes',
        'password',
        'email_verified_at',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the formations that this student is enrolled in
     */
    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(Formation::class, 'formation_student');
    }

    /**
     * Get the educational support subscriptions for this student
     */
    public function educationalSupportSubscriptions(): HasMany
    {
        return $this->hasMany(EducationalSupportSubscription::class);
    }
}
