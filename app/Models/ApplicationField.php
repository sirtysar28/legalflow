<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationField extends Model
{
    protected $fillable = ['application_id', 'field_name', 'field_value'];
}
