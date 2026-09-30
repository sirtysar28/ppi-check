<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finding extends Model
{
    protected $fillable = [
        'finding_number', 'audit_id', 'category_id', 'question_id', 'unit_id',
        'description', 'location', 'severity', 'photo_path', 'recommendation',
        'due_date', 'status',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public const SEVERITIES = [
        'minor'  => 'Minor',
        'mayor'  => 'Mayor',
        'kritis' => 'Kritis',
    ];

    public const STATUSES = [
        'open'     => 'Open',
        'progress' => 'Progress',
        'closed'   => 'Closed',
    ];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function category()
    {
        return $this->belongsTo(AuditCategory::class, 'category_id');
    }

    public function question()
    {
        return $this->belongsTo(AuditQuestion::class, 'question_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function latestFollowUp()
    {
        return $this->hasOne(FollowUp::class)->latestOfMany();
    }

    public function verifications()
    {
        return $this->hasMany(Verification::class);
    }

    public function severityColor(): string
    {
        return match ($this->severity) {
            'kritis' => 'danger',
            'mayor'  => 'warning',
            default  => 'info',
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'closed'   => 'success',
            'progress' => 'warning',
            default    => 'danger',
        };
    }
}
