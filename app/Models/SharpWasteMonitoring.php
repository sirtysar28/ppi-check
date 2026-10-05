<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SharpWasteMonitoring extends Model
{
    protected $fillable = [
        'monitoring_number', 'unit_id', 'officer_name', 'monitoring_date',
        'total_items', 'conform_items', 'nonconform_items',
        'compliance_percentage', 'grade', 'user_id', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'monitoring_date' => 'date',
            'compliance_percentage' => 'decimal:2',
        ];
    }

    /**
     * Item pernyataan baku Lembar Monitoring Penanganan Limbah Benda Tajam.
     */
    public const STATEMENTS = [
        'Jarum suntik bekas pakai tidak ditutup kembali (no recapping)',
        'Benda tajam bekas pakai tidak diberikan secara langsung kepada orang lain (tidak hand-to-hand)',
        'Jika harus memberikan benda tajam ke orang lain gunakan container',
        'Limbah benda tajam dibuang ke dalam safety box (tahan tusuk dan tahan bocor)',
        'Safety box ditutup rapat atau disegel saat telah terisi maksimal 3/4 dan dibuang ke tempat penyimpanan sementara limbah medis',
        'Tidak dilakukan pembengkokan (bending) atau pematahan jarum',
        'Jarum tidak dilepas dari spuit secara manual',
        'Safety box tersedia di setiap titik pelayanan',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SharpWasteMonitoringItem::class, 'monitoring_id')->orderBy('order');
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
