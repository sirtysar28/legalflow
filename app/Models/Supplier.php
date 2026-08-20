<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name', 'npwp', 'address', 'phone', 'email', 'contact_person',
        'is_registered', 'assessment_available', 'assessment_score',
        'assessment_date', 'risk_level', 'data_complete', 'documents_complete', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_registered' => 'boolean',
            'assessment_available' => 'boolean',
            'assessment_score' => 'decimal:2',
            'assessment_date' => 'date',
            'data_complete' => 'boolean',
            'documents_complete' => 'boolean',
        ];
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    /**
     * Check Supplier Assessment lolos jika supplier terdaftar,
     * assessment tersedia, data lengkap, dan dokumen lengkap.
     */
    public function assessmentPassed(): bool
    {
        return $this->is_registered
            && $this->assessment_available
            && $this->data_complete
            && $this->documents_complete;
    }

    public function riskLevel(): string
    {
        return $this->risk_level ?: 'UNKNOWN';
    }

    public function riskLabel(): string
    {
        return match ($this->riskLevel()) {
            'LOW' => 'Low Risk',
            'MEDIUM' => 'Medium Risk',
            'HIGH' => 'High Risk',
            default => 'Belum Dinilai',
        };
    }

    public function riskColor(): string
    {
        return match ($this->riskLevel()) {
            'LOW' => 'success',
            'MEDIUM' => 'warning',
            'HIGH' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Ringkasan Supplier Assessment System untuk reviewer agreement:
     * Status (lolos/tidak), Risk, Score.
     */
    public function assessmentSummary(): array
    {
        return [
            'passed' => $this->assessmentPassed(),
            'risk'   => $this->riskLabel(),
            'risk_color' => $this->riskColor(),
            'score'  => $this->assessment_score !== null
                ? rtrim(rtrim(number_format((float) $this->assessment_score, 2, '.', ''), '0'), '.')
                : null,
            'date'   => $this->assessment_date?->format('d M Y'),
        ];
    }
}
