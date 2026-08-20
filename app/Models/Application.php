<?php

namespace App\Models;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\DocumentRequirement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Application extends Model
{
    protected $fillable = [
        'application_number',
        'application_type',
        'user_id',
        'department_id',
        'permit_type_id',
        'supplier_id',
        'title',
        'description',
        'status',
        'submitted_at',
        'approved_at',
        'rejected_at',
        'valid_until',
    ];

    protected function casts(): array
    {
        return [
            'application_type' => ApplicationType::class,
            'status' => ApplicationStatus::class,
            'submitted_at' => 'datetime',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'valid_until' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function permitType()
    {
        return $this->belongsTo(PermitType::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function fields()
    {
        return $this->hasMany(ApplicationField::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function reviews()
    {
        return $this->hasMany(ApplicationReview::class)->latest('reviewed_at');
    }

    public function histories()
    {
        return $this->hasMany(ApplicationHistory::class)->latest();
    }

    public function accessRequests()
    {
        return $this->hasMany(DocumentAccessRequest::class)->latest();
    }

    public function scopeOfType($query, ApplicationType $type)
    {
        return $query->where('application_type', $type->value);
    }

    public function scopeStatus($query, ApplicationStatus $status)
    {
        return $query->where('status', $status->value);
    }

    public function scopeMine($query, User $user)
    {
        return $query->where('user_id', $user->id);
    }

    /** Izin/kontrak disetujui yang berakhir dalam X hari ke depan. */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->whereIn('status', [ApplicationStatus::APPROVED->value, ApplicationStatus::EXPIRED->value])
            ->whereDate('valid_until', '>=', today())
            ->whereDate('valid_until', '<=', today()->addDays($days));
    }

    /** Kontrak/izin disetujui dan masih berlaku (atau tanpa batas waktu). */
    public function scopeActive($query)
    {
        return $query->status(ApplicationStatus::APPROVED)
            ->where(fn ($q) => $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', today()));
    }

    /**
     * Sinkronisasi status KADALUARSA: pengajuan APPROVED yang masa berlakunya
     * sudah lewat otomatis berubah EXPIRED (+ tercatat di audit trail).
     * Dipanggil saat membuka dashboard / list / browse dokumen.
     */
    public static function syncExpiry(): int
    {
        $expired = static::query()
            ->status(ApplicationStatus::APPROVED)
            ->whereDate('valid_until', '<', today())
            ->pluck('id');

        if ($expired->isEmpty()) {
            return 0;
        }

        foreach ($expired->chunk(50) as $ids) {
            static::query()->whereIn('id', $ids)->update(['status' => ApplicationStatus::EXPIRED->value]);

            foreach ($ids as $id) {
                ApplicationHistory::create([
                    'application_id' => $id,
                    'user_id'        => null,
                    'action'         => 'Masa berlaku berakhir',
                    'old_status'     => ApplicationStatus::APPROVED->value,
                    'new_status'     => ApplicationStatus::EXPIRED->value,
                    'notes'          => 'Status otomatis menjadi Kadaluarsa karena masa berlaku izin/kontrak telah berakhir.',
                ]);
            }
        }

        return $expired->count();
    }

    /**
     * Daftar nama dokumen wajib sesuai jenis pengajuan & jenis izin.
     */
    public function requiredDocumentNames(): Collection
    {
        return DocumentRequirement::query()
            ->where('is_active', true)
            ->where('application_type', $this->application_type->value)
            ->where('is_required', true)
            ->when(
                $this->application_type === ApplicationType::PERMIT,
                fn ($q) => $q->where(fn ($w) => $w->whereNull('permit_type_id')
                    ->orWhere('permit_type_id', $this->permit_type_id))
            )
            ->pluck('document_name');
    }

    /**
     * Progress kelengkapan dokumen wajib: ['total', 'uploaded', 'percent']
     */
    public function documentProgress(): array
    {
        $required = $this->requiredDocumentNames();
        $uploaded = $this->documents->pluck('document_type')->unique();
        $done = $required->intersect($uploaded)->count();
        $total = $required->count();

        return [
            'total'    => $total,
            'uploaded' => $done,
            'percent'  => $total > 0 ? (int) round($done / $total * 100) : 0,
        ];
    }

    /** Ringkasan nilai kontrak + mata uang (untuk agreement). */
    public function contractSummary(): ?string
    {
        if ($this->application_type !== ApplicationType::AGREEMENT) {
            return null;
        }

        $value = $this->fieldValue('nilai_kontrak');
        if (blank($value)) {
            return null;
        }

        $currency = $this->fieldValue('mata_uang') ?: 'IDR';

        return $currency === 'IDR'
            ? 'Rp ' . $value
            : $currency . ' ' . $value;
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [
            ApplicationStatus::DRAFT,
            ApplicationStatus::REVISION_REQUESTED,
        ], true);
    }

    public function typeLabel(): string
    {
        return $this->application_type->label();
    }

    public function statusLabel(): string
    {
        return $this->status->label();
    }

    public function statusColor(): string
    {
        return $this->status->color();
    }

    public function fieldValue(string $name): ?string
    {
        return $this->fields->firstWhere('field_name', $name)?->field_value;
    }

    /**
     * Generate nomor pengajuan: LF-PRM-2026-0001 / LF-AGR-2026-0002
     */
    public static function generateNumber(int $id, ApplicationType $type): string
    {
        return sprintf('LF-%s-%s-%04d', $type->prefix(), now()->format('Y'), $id);
    }
}
