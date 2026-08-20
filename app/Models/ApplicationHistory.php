<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationHistory extends Model
{
    protected $fillable = [
        'application_id',
        'user_id',
        'action',
        'old_status',
        'new_status',
        'notes',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
