<?php

namespace App\Support;

/**
 * Data master sesuai dokumen "Update tgl 5 Okt 2026":
 * - Daftar nama ruangan (44)
 * - Item penilaian Cuci Tangan (5 Momen WHO)
 * - Item penilaian APD (tindakan + jenis APD yang dinilai)
 */
class MasterData
{
    /** Nama ruangan sesuai Word (tgl 5 Okt 2026). */
    public const UNITS = [
        'IGD P1',
        'IGD P2',
        'IGD Pediatrik',
        'RUTE',
        'LABORATORIUM',
        'RADIOLOGI',
        'ALBUCACIS',
        'ANGGREK',
        'ANTARES',
        'AVEROSE',
        'BENANG KELAMBU',
        'BURN UNIT',
        'CVCU',
        'GILI GEDE',
        'GILI NANGGU',
        'GILI TRAWANGAN',
        'HCU 1',
        'HCU 2',
        'HCU 3',
        'ICU 1',
        'ICU 2',
        'ICU ISO',
        'ICU NON BEDAH',
        'MANGKU SAKTI',
        'MATA JITU',
        'NICU',
        'OTAK KOKO',
        'PANTAI LAKEY',
        'PANTAI PINK',
        'PANTAI SENGGIGI',
        'TANJUNG AN',
        'PICU 1',
        'PICU 2',
        'RINJANI',
        'RUANG RAWAT STROKE',
        'WARM ZONE',
        'SEGARA ANAK',
        'SENDANG GILE',
        'SCU',
        'TIU KELEP',
        'TERATAI/GILI MOYO',
        'HEMODIALISA',
        'POLI KANDUNGAN',
        'POLI ORTO',
    ];

    /** Item penilaian Cuci Tangan (5 Momen Kebersihan Tangan WHO). */
    public const HAND_HYGIENE_QUESTIONS = [
        'Melakukan cuci tangan sebelum kontak dengan pasien',
        'Melakukan cuci tangan sebelum tindakan aseptik',
        'Melakukan cuci tangan setelah terpapar cairan tubuh pasien',
        'Melakukan cuci tangan setelah kontak dengan pasien',
        'Melakukan cuci tangan setelah kontak dengan lingkungan sekitar pasien',
    ];

    /** Jenis APD yang dinilai (kolom penilaian pada audit APD). */
    public const APD_TYPES = [
        'Sarung Tangan',
        'Masker',
        'Goggle',
        'Apron',
        'Tutup Kepala',
        'Sepatu Boot',
    ];

    /** Item tindakan pada audit APD (pilih salah satu saat observasi). */
    public const APD_ACTIONS = [
        'Memandikan',
        'Menolong BAB',
        'Menolong BAK',
        'Oral Hygiene',
        'Suction',
        'Mengambil darah Vena',
        'Perawatan Luka Mayor',
        'Perawatan Luka Minor',
        'Perawatan Luka Infeksius',
        'Mengukur TTV',
        'Injeksi',
        'Pemasangan CVC Line',
        'Intubasi',
        'Memasang Infus',
        'Memasang Kateter',
        'Melap Meja, Monitor, Syring Pump di Pasien',
        'Membersihkan Peralatan Sehabis Pakai',
        'Transportasi Pasien',
        'Vulva / Penis Hygiene',
    ];

    /** Item checklist audit APD = penggunaan tiap jenis APD. */
    public static function apdQuestions(): array
    {
        return array_map(fn (string $apd) => "Penggunaan {$apd} sesuai tindakan", self::APD_TYPES);
    }

    /** Kode unit otomatis dari nama ruangan. */
    public static function unitCode(string $name): string
    {
        return str_replace('-', '_', strtoupper(\Illuminate\Support\Str::slug(str_replace('/', ' ', $name))));
    }
}
