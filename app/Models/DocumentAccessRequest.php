<?php

namespace App\Models;

use App\Enums\AccessStatus;
use Illuminate\Database\Eloquent\Model;

class DocumentAccessRequest extends Model
{
    protected $fillable = [
        'application_id',
        'requested_by',
        'reason',
        'status',
        'access_type',
        'reviewed_by',
        'review_notes',
        'approved_at',
        'expired_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => AccessStatus::class,
            'approved_at' => 'datetime',
            'expired_at' => 'date',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isActive(): bool
    {
        return $this->status === AccessStatus::APPROVED
            && $this->expired_at !== null
            && $this->expired_at->endOfDay()->isFuture();
    }

    /**
     * Tandai otomatis ACCESS_APPROVED yang sudah lewat masa berlaku.
     */
    public static function syncExpiry(): void
    {
        static::where('status', AccessStatus::APPROVED->value)
            ->whereNotNull('expired_at')
            ->whereDate('expired_at', '<', now()->toDateString())
            ->update(['status' => AccessStatus::EXPIRED->value]);
    }
}
