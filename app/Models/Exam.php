<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = [
        'subject_id',
        'title',
        'category',
        'education_level',
        'duration_minutes',
        'passing_grade',
        'start_time',
        'end_time',
        'status',
        'shuffle_questions',
        'shuffle_options',
        'max_violations',
        'created_by',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'duration_minutes' => 'integer',
        'passing_grade' => 'float',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function tokens()
    {
        return $this->hasMany(Token::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
