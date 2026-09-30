<?php

namespace App\Support;

use App\Models\Setting;

class Ppi
{
    /** Warna badge untuk grade kepatuhan. */
    public static function gradeColor(?string $grade): string
    {
        return match ($grade) {
            'Sangat Baik' => 'success',
            'Baik' => 'primary',
            'Cukup' => 'warning',
            'Perlu Perbaikan' => 'danger',
            default => 'secondary',
        };
    }

    /** Ambang batas dari konfigurasi (editable admin). */
    public static function thresholds(): array
    {
        return [
            'sangat_baik' => (float) Setting::get('threshold_sangat_baik', '90'),
            'baik' => (float) Setting::get('threshold_baik', '80'),
            'cukup' => (float) Setting::get('threshold_cukup', '70'),
        ];
    }

    /** Hitung kepatuhan = (sesuai / dinilai) x 100. N/A tidak dihitung. */
    public static function compliance(int|float $conform, int|float $assessed): float
    {
        if ($assessed <= 0) {
            return 0.0;
        }

        return round(($conform / $assessed) * 100, 2);
    }
}
