<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $fillable = [
        'exam_id',
        'token_code',
        'valid_until',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isExpired(): bool
    {
        return Carbon::now()->greaterThan($this->valid_until) || !$this->is_active;
    }

    public function remainingSeconds(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        return max(0, Carbon::now()->diffInSeconds($this->valid_until, false));
    }

    public static function generateCode(): string
    {
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }
}
