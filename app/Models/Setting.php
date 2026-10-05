<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    public const DEFAULTS = [
        'facility_name'     => 'Rumah Sakit Contoh',
        'facility_address'  => 'Jl. Contoh No. 1, Indonesia',
        'threshold_sangat_baik' => '90',
        'threshold_baik'    => '80',
        'threshold_cukup'   => '70',
        'follow_up_deadline_days' => '7',

        // Logo aplikasi (path pada disk public, null = pakai logo bawaan)
        'app_logo'          => null,

        // SMTP (dikendalikan via menu Pengaturan)
        'mail_mailer'       => 'smtp',
        'mail_host'         => '',
        'mail_port'         => '587',
        'mail_encryption'   => 'tls',
        'mail_username'     => '',
        'mail_password'     => '',
        'mail_from_address' => '',
        'mail_from_name'    => 'PPI Check',
    ];

    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $row = static::where('key', $key)->first();
        return $row?->value ?? $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * URL logo aplikasi (hasil upload di menu Pengaturan) atau logo bawaan.
     */
    public static function logoUrl(): string
    {
        try {
            $path = static::get('app_logo');

            if ($path && Storage::disk('public')->exists($path)) {
                return Storage::disk('public')->url($path);
            }
        } catch (\Throwable) {
            // tabel settings belum tersedia (mis. saat instalasi awal) — pakai bawaan
        }

        return asset('images/logo.png');
    }

    /**
     * Grade berdasarkan konfigurasi ambang batas admin.
     */
    public static function grade(float $percentage): string
    {
        if ($percentage >= (float) self::get('threshold_sangat_baik', '90')) {
            return 'Sangat Baik';
        }
        if ($percentage >= (float) self::get('threshold_baik', '80')) {
            return 'Baik';
        }
        if ($percentage >= (float) self::get('threshold_cukup', '70')) {
            return 'Cukup';
        }

        return 'Perlu Perbaikan';
    }
}
