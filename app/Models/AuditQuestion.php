<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditQuestion extends Model
{
    protected $fillable = ['category_id', 'question', 'weight', 'order', 'is_active'];

    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(AuditCategory::class, 'category_id');
    }

    public function answers()
    {
        return $this->hasMany(AuditAnswer::class, 'question_id');
    }
}
