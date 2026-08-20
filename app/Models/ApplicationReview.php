<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationReview extends Model
{
    public const ACTION_START = 'start_review';
    public const ACTION_APPROVE = 'approve';
    public const ACTION_REVISION = 'request_revision';
    public const ACTION_REJECT = 'reject';

    protected $fillable = [
        'application_id',
        'reviewer_id',
        'action',
        'status',
        'notes',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            self::ACTION_START => 'Mulai Review',
            self::ACTION_APPROVE => 'Setujui',
            self::ACTION_REVISION => 'Minta Revisi',
            self::ACTION_REJECT => 'Tolak',
            default => $this->action,
        };
    }
}
