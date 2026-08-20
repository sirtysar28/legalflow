<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentRequirement extends Model
{
    protected $fillable = ['application_type', 'permit_type_id', 'document_name', 'is_required', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_required' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function permitType()
    {
        return $this->belongsTo(PermitType::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
