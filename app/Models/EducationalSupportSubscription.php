<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EducationalSupportSubscription extends Model
{
    protected $fillable = [
        'student_id',
        'educational_subject_id',
        'start_date',
        'end_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'status' => 'string'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(EducationalSubject::class, 'educational_subject_id');
    }

    public function payment(): HasOne
    {
        return $this->hasOne(EducationalSupportPayment::class, 'subscription_id');
    }
}
