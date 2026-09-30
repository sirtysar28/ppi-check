<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
    protected $fillable = [
        'audit_number', 'category_id', 'unit_id', 'auditor_id', 'audit_date',
        'shift', 'officer_name', 'profession_id', 'action_type', 'apd_type_id',
        'waste_type_id', 'total_items', 'conform_items', 'nonconform_items',
        'na_items', 'compliance_percentage', 'grade', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'audit_date' => 'date',
            'compliance_percentage' => 'decimal:2',
        ];
    }

    public const SHIFTS = ['pagi' => 'Pagi', 'siang' => 'Siang', 'malam' => 'Malam'];

    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    public function category()
    {
        return $this->belongsTo(AuditCategory::class, 'category_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function auditor()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function profession()
    {
        return $this->belongsTo(Profession::class);
    }

    public function apdType()
    {
        return $this->belongsTo(ApdType::class);
    }

    public function wasteType()
    {
        return $this->belongsTo(WasteType::class);
    }

    public function answers()
    {
        return $this->hasMany(AuditAnswer::class);
    }

    public function findings()
    {
        return $this->hasMany(Finding::class);
    }

    public function getShiftLabelAttribute(): string
    {
        return self::SHIFTS[$this->shift] ?? ucfirst($this->shift);
    }

    public function gradeColor(): string
    {
        return match ($this->grade) {
            'Sangat Baik' => 'success',
            'Baik' => 'info',
            'Cukup' => 'warning',
            default => 'danger',
        };
    }
}
