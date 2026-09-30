<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditCategory extends Model
{
    protected $fillable = ['code', 'name', 'icon', 'description', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function questions()
    {
        return $this->hasMany(AuditQuestion::class, 'category_id')->orderBy('order');
    }

    public function activeQuestions()
    {
        return $this->questions()->where('is_active', true);
    }

    public function audits()
    {
        return $this->hasMany(Audit::class);
    }

    public function findings()
    {
        return $this->hasMany(Finding::class);
    }
}
