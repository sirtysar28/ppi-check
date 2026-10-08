<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Satu baris observasi pada Lembar Audit Cuci Tangan.
 * Tiap observasi mencatat momen (5 Momen Kebersihan Tangan WHO)
 * dan tindakan kebersihan tangan yang dilakukan petugas.
 */
class HandHygieneObservation extends Model
{
    /** 5 Momen Kebersihan Tangan WHO. */
    public const MOMENTS = [
        'seb_pasien' => 'Sebelum kontak dengan pasien',
        'seb_aseptik' => 'Sebelum tindakan aseptik',
        'set_cairan_tubuh' => 'Setelah kontak darah / cairan tubuh',
        'set_pasien' => 'Setelah kontak dengan pasien',
        'set_lingkungan' => 'Setelah kontak lingkungan pasien',
    ];

    /** Label singkat momen (untuk tombol/tabel). */
    public const MOMENT_SHORT = [
        'seb_pasien' => 'Seb. pasien',
        'seb_aseptik' => 'Seb. aseptik',
        'set_cairan_tubuh' => 'Set. darah/cairan tbh',
        'set_pasien' => 'Set. pasien',
        'set_lingkungan' => 'Set. lingk. pasien',
    ];

    /** Jenis tindakan kebersihan tangan. */
    public const ACTIONS = [
        'hr' => 'Hand Rub (HR)',
        'hw' => 'Hand Wash (HW)',
        'tidak' => 'Tidak melakukan',
        'set_lepas_sarung_tangan' => 'Cuci tangan setelah lepas sarung tangan',
    ];

    /** Label singkat tindakan (untuk tombol/tabel). */
    public const ACTION_SHORT = [
        'hr' => 'HR',
        'hw' => 'HW',
        'tidak' => 'Tidak',
        'set_lepas_sarung_tangan' => 'Set. lepas sarung tangan',
    ];

    /** Tindakan yang dihitung PATUH (melakukan kebersihan tangan). */
    public const COMPLIANT_ACTIONS = ['hr', 'hw', 'set_lepas_sarung_tangan'];

    protected $fillable = ['audit_id', 'sequence', 'moment', 'action'];

    public function audit()
    {
        return $this->belongsTo(Audit::class);
    }

    public function getMomentLabelAttribute(): string
    {
        return self::MOMENTS[$this->moment] ?? $this->moment;
    }

    public function getMomentShortLabelAttribute(): string
    {
        return self::MOMENT_SHORT[$this->moment] ?? $this->moment;
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? $this->action;
    }

    public function getActionShortLabelAttribute(): string
    {
        return self::ACTION_SHORT[$this->action] ?? $this->action;
    }

    /** Patuh bila petugas melakukan kebersihan tangan (HR / HW / setelah lepas sarung tangan). */
    public function getIsCompliantAttribute(): bool
    {
        return in_array($this->action, self::COMPLIANT_ACTIONS, true);
    }
}
