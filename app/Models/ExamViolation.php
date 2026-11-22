<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamViolation extends Model
{
    protected $fillable = [
        'attempt_id',
        'user_id',
        'violation_type',
        'details',
    ];

    public function attempt()
    {
        return $this->belongsTo(ExamAttempt::class, 'attempt_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
