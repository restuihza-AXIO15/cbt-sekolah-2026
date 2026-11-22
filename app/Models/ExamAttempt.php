<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ExamAttempt extends Model
{
    protected $fillable = [
        'exam_id',
        'user_id',
        'token_used',
        'question_order',
        'started_at',
        'duration_minutes',
        'end_at',
        'finished_at',
        'status',
        'violations_count',
        'last_heartbeat',
        'score',
        'correct_count',
        'incorrect_count',
        'unanswered_count',
    ];

    protected $casts = [
        'question_order' => 'array',
        'started_at' => 'datetime',
        'end_at' => 'datetime',
        'finished_at' => 'datetime',
        'last_heartbeat' => 'datetime',
        'score' => 'float',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(ExamAnswer::class, 'attempt_id');
    }

    public function violations()
    {
        return $this->hasMany(ExamViolation::class, 'attempt_id');
    }

    public function isFinished(): bool
    {
        return $this->status === 'finished';
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked';
    }

    public function isTimeExpired(): bool
    {
        return Carbon::now()->greaterThan($this->end_at);
    }

    public function remainingSeconds(): int
    {
        if ($this->isFinished()) {
            return 0;
        }
        $remaining = Carbon::now()->diffInSeconds($this->end_at, false);
        return max(0, $remaining);
    }

    /**
     * Compute and update score, correct_count, incorrect_count on backend
     */
    public function calculateResult(): void
    {
        $exam = $this->exam;
        $questions = Question::where('subject_id', $exam->subject_id)->get()->keyBy('id');
        $answers = $this->answers()->get()->keyBy('question_id');

        $totalQuestions = count($this->question_order ?: $questions->keys());
        $correct = 0;
        $incorrect = 0;
        $unanswered = 0;
        $totalWeight = 0;
        $earnedWeight = 0;

        foreach ($questions as $qId => $q) {
            $totalWeight += $q->score_weight;
            if (isset($answers[$qId]) && !empty($answers[$qId]->selected_option)) {
                $ans = $answers[$qId];
                if (strtoupper($ans->selected_option) === strtoupper($q->correct_answer)) {
                    $correct++;
                    $earnedWeight += $q->score_weight;
                    if (!$ans->is_correct) {
                        $ans->update(['is_correct' => true]);
                    }
                } else {
                    $incorrect++;
                    if ($ans->is_correct) {
                        $ans->update(['is_correct' => false]);
                    }
                }
            } else {
                $unanswered++;
            }
        }

        $finalScore = $totalWeight > 0 ? round(($earnedWeight / $totalWeight) * 100, 2) : 0;

        $this->update([
            'score' => $finalScore,
            'correct_count' => $correct,
            'incorrect_count' => $incorrect,
            'unanswered_count' => $unanswered,
            'finished_at' => Carbon::now(),
            'status' => 'finished',
        ]);
    }

    public function durationFormatted(): string
    {
        if (!$this->finished_at || !$this->started_at) {
            return '-';
        }
        $seconds = $this->started_at->diffInSeconds($this->finished_at);
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return "{$minutes} menit {$remainingSeconds} detik";
    }

    public function isPassed(): bool
    {
        $passingGrade = $this->exam ? $this->exam->passing_grade : 75;
        return $this->score >= $passingGrade;
    }
}
