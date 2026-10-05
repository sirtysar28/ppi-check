<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApdAction extends Model
{
    protected $fillable = ['name', 'order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }
}
