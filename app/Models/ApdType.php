<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApdType extends Model
{
    protected $fillable = ['name', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }
}
