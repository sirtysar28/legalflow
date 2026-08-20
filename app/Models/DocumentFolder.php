<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentFolder extends Model
{
    protected $fillable = ['name', 'parent_id', 'created_by'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('name');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Path lengkap folder: "Folder Induk / Sub Folder / ...".
     */
    public function fullPath(string $separator = ' / '): string
    {
        $parts = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($parts, $parent->name);
            $parent = $parent->parent;
        }

        return implode($separator, $parts);
    }
}
