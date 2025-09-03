<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChildhoodSubscription extends Model
{
    protected $fillable = [
        'student_id',
        'childhood_subject_id',
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

    public function childhoodSubject(): BelongsTo
    {
        return $this->belongsTo(ChildhoodSubject::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(ChildhoodPayment::class, 'subscription_id');
    }
}
