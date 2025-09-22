<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Formation extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'teachers',
        'duration',
        'level',
        'price',
        'school_percent',
        'teacher_percent',
        'status',
        'enrolled_students'
    ];

    protected $casts = [
        'teachers' => 'array', // Automatically cast JSON to array
        'school_percent' => 'decimal:2',
        'teacher_percent' => 'decimal:2',
        'enrolled_students' => 'integer'
    ];

    protected $attributes = [
        'enrolled_students' => 0,
        'status' => 'Active'
    ];

    /**
     * Get the students enrolled in this formation
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'formation_student');
    }
}