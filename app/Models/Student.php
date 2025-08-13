<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
