<?php

namespace Database\Seeders;

use App\Models\ApdType;
use App\Models\AuditCategory;
use App\Models\AuditQuestion;
use App\Models\Profession;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use App\Models\WasteType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // ==== Units ====
        $units = [
            ['code' => 'ICU', 'name' => 'Intensive Care Unit (ICU)', 'head_name' => 'Ns. Dewi Lestari'],
            ['code' => 'IGD', 'name' => 'Instalasi Gawat Darurat (IGD)', 'head_name' => 'Ns. Budi Santoso'],
            ['code' => 'RIN', 'name' => 'Rawat Inap (R1)', 'head_name' => 'Ns. Sari Wulandari'],
            ['code' => 'POL', 'name' => 'Poliklinik Umum', 'head_name' => 'Ns. Andi Pratama'],
            ['code' => 'OK', 'name' => 'Kamar Operasi (OK)', 'head_name' => 'Ns. Rina Marlina'],
            ['code' => 'LAB', 'name' => 'Laboratorium', 'head_name' => 'An. Joko Susilo'],
            ['code' => 'FRM', 'name' => 'Farmasi', 'head_name' => 'Apt. Maya Sari'],
            ['code' => 'HDL', 'name' => 'Hemodialisa', 'head_name' => 'Ns. Eko Purnomo'],
            ['code' => 'PRT', 'name' => 'Perinatologi / NICU', 'head_name' => 'Ns. Fitri Handayani'],
            ['code' => 'CSSD', 'name' => 'CSSD (Sterilisasi)', 'head_name' => 'Ns. Hendra Wijaya'],
        ];
        foreach ($units as $u) {
            Unit::updateOrCreate(['code' => $u['code']], $u);
        }

        // ==== Professions ====
        foreach (['Perawat', 'Dokter', 'Bidan', 'Tenaga Kefarmasian', 'Analis Laboratorium', 'Ahli Gizi', 'Petugas Kebersihan', 'Fisioterapis', 'Radiografer'] as $p) {
            Profession::updateOrCreate(['name' => $p]);
        }

        // ==== APD Types ====
        $apd = [
            ['name' => 'Masker', 'description' => 'Masker bedah / N95'],
            ['name' => 'Sarung Tangan', 'description' => 'Sarung tangan sekali pakai'],
            ['name' => 'Gown', 'description' => 'Gaun pelindung'],
            ['name' => 'Apron', 'description' => 'Celemek plastik'],
            ['name' => 'Face Shield', 'description' => 'Pelindung wajah'],
            ['name' => 'Goggle', 'description' => 'Kacamata pelindung'],
            ['name' => 'Sepatu Pelindung', 'description' => 'Sepatu boot / cover sepatu'],
        ];
        foreach ($apd as $a) {
            ApdType::updateOrCreate(['name' => $a['name']], $a);
        }

        // ==== Waste Types ====
        $waste = [
            ['name' => 'Limbah Infeksius', 'color_code' => 'Kuning', 'description' => 'Kain/bahan yang terkontaminasi darah/cairan tubuh'],
            ['name' => 'Limbah Non Infeksius', 'color_code' => 'Hitam', 'description' => 'Sampah domestik / kertas'],
            ['name' => 'Benda Tajam', 'color_code' => 'Kuning (safety box)', 'description' => 'Jarum, lancet, blade'],
            ['name' => 'Limbah Farmasi', 'color_code' => 'Coklat', 'description' => 'Obat kadaluarsa, sisa obat'],
            ['name' => 'Limbah B3', 'color_code' => 'Merah', 'description' => 'Bahan berbahaya dan beracun'],
        ];
        foreach ($waste as $w) {
            WasteType::updateOrCreate(['name' => $w['name']], $w);
        }

        // ==== Audit Categories + Questions (Master Instrumen) ====
        $categories = [
            [
                'code' => 'cuci-tangan', 'name' => 'Audit Cuci Tangan', 'icon' => 'bi-droplet-half',
                'description' => 'Audit kepatuhan kebersihan tangan (hand hygiene) sesuai 5 momen WHO.',
                'questions' => [
                    'Tersedia fasilitas cuci tangan (wastafel/ tempat cuci tangan)',
                    'Air mengalir tersedia',
                    'Sabun tersedia',
                    'Handrub (alkohol based hand rub) tersedia',
                    'Petugas melakukan kebersihan tangan sebelum tindakan / kontak pasien',
                    'Petugas melakukan kebersihan tangan setelah tindakan / kontak pasien',
                    'Teknik cuci tangan dilakukan sesuai prosedur (6 langkah)',
                    'Petugas mengeringkan tangan dengan handuk sekali pakai / pengering',
                    'Tidak ada perhiasan (cincin, gelang, jam) pada tangan petugas',
                    'Kuku pendek dan bersih',
                ],
            ],
            [
                'code' => 'apd', 'name' => 'Audit APD', 'icon' => 'bi-person-badge',
                'description' => 'Audit penggunaan Alat Pelindung Diri (APD) sesuai jenis tindakan.',
                'questions' => [
                    'APD tersedia di unit',
                    'APD sesuai dengan jenis tindakan',
                    'APD digunakan sebelum tindakan',
                    'APD digunakan dengan benar (sesuai prosedur pemakaian)',
                    'APD dilepas sesuai prosedur',
                    'APD sekali pakai tidak digunakan kembali',
                    'APD dibuang pada tempat yang sesuai',
                ],
            ],
            [
                'code' => 'sampah', 'name' => 'Audit Pemilahan Sampah', 'icon' => 'bi-recycle',
                'description' => 'Audit pemilahan dan pengelolaan limbah medis dan non medis.',
                'questions' => [
                    'Tempat sampah tersedia di setiap area',
                    'Label tempat sampah tersedia dan terbaca',
                    'Warna tempat sampah sesuai jenis limbah',
                    'Sampah dipilah sesuai jenisnya',
                    'Safety box tersedia',
                    'Benda tajam dimasukkan ke safety box',
                    'Sampah tidak tercampur antar jenis',
                    'Tempat sampah ditutup dan tidak melebihi kapasitas 3/4',
                    'Sampah medis diangkut sesuai jadwal dengan alat khusus',
                ],
            ],
        ];

        foreach ($categories as $cat) {
            $category = AuditCategory::updateOrCreate(
                ['code' => $cat['code']],
                [
                    'name' => $cat['name'],
                    'icon' => $cat['icon'],
                    'description' => $cat['description'],
                    'is_active' => true,
                ]
            );

            foreach ($cat['questions'] as $i => $q) {
                AuditQuestion::updateOrCreate(
                    ['category_id' => $category->id, 'question' => $q],
                    ['weight' => 1, 'order' => $i + 1, 'is_active' => true]
                );
            }
        }

        // ==== Settings ====
        foreach (Setting::DEFAULTS as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // ==== Users ====
        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@ppicheck.test', 'role' => User::ROLE_SUPER_ADMIN, 'unit_id' => null],
            ['name' => 'Admin PPI', 'email' => 'adminppi@ppicheck.test', 'role' => User::ROLE_ADMIN_PPI, 'unit_id' => null],
            ['name' => 'Andi Auditor', 'email' => 'auditor@ppicheck.test', 'role' => User::ROLE_AUDITOR, 'unit_id' => null],
            ['name' => 'Rina Auditor', 'email' => 'rina.auditor@ppicheck.test', 'role' => User::ROLE_AUDITOR, 'unit_id' => null],
            ['name' => 'Kepala Unit IGD', 'email' => 'unit.igd@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => 'IGD'],
            ['name' => 'Kepala Unit ICU', 'email' => 'unit.icu@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => 'ICU'],
            ['name' => 'Kepala Rawat Inap', 'email' => 'unit.rin@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => 'RIN'],
            ['name' => 'Kepala Poli Umum', 'email' => 'unit.pol@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => 'POL'],
        ];

        $professionId = Profession::where('name', 'Perawat')->first()->id;

        foreach ($users as $u) {
            $unitId = isset($u['unit_code']) ? Unit::where('code', $u['unit_code'])->first()->id : null;
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => $u['role'],
                    'unit_id' => $unitId,
                    'profession_id' => $u['role'] === User::ROLE_AUDITOR ? $professionId : null,
                    'is_active' => true,
                ]
            );
        }
    }
}
