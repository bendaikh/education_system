<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role',
        'department',
        'contact_phone',
        'contact_email',
        'image',
    ];

    public function educationalSubjects(): BelongsToMany
    {
        return $this->belongsToMany(EducationalSubject::class, 'educational_subject_teacher');
    }
}
