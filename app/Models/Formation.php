<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'status',
        'enrolled_students'
    ];

    protected $casts = [
        'teachers' => 'array', // Automatically cast JSON to array
        'enrolled_students' => 'integer'
    ];

    protected $attributes = [
        'enrolled_students' => 0,
        'status' => 'Active'
    ];
}