<?php

namespace App\Models;

use App\Enums\PermitCategory;
use Illuminate\Database\Eloquent\Model;

class PermitType extends Model
{
    protected $fillable = ['name', 'category', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'category' => PermitCategory::class,
            'is_active' => 'boolean',
        ];
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function requirements()
    {
        return $this->hasMany(DocumentRequirement::class);
    }

    public function categoryLabel(): string
    {
        return $this->category?->label() ?? 'Tanpa Kategori';
    }
}
