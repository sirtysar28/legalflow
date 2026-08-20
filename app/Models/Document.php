<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public const STATUS_UPLOADED = 'UPLOADED';
    public const STATUS_ISSUED = 'ISSUED';

    protected $fillable = [
        'application_id',
        'document_type',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'version',
        'status',
        'folder',
        'uploaded_by',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function sizeHuman(): string
    {
        $bytes = (float) $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 1) . ' ' . $units[$i];
    }
}
