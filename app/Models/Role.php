<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    public const NAME_USER = 'user';

    public const NAME_LEGAL = 'legal';

    public const NAME_ADMIN = 'admin';

    public const NAME_SUPER_ADMIN = 'super_admin';

    protected $fillable = ['name', 'label'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
