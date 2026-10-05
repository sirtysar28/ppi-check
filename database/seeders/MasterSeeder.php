<?php

namespace Database\Seeders;

use App\Models\ApdAction;
use App\Models\ApdType;
use App\Models\AuditCategory;
use App\Models\AuditQuestion;
use App\Models\Profession;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use App\Models\WasteType;
use App\Support\MasterData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MasterSeeder extends Seeder
{
    public function run(): void
    {
        // ==== Units (44 ruangan sesuai dokumen Update 5 Okt 2026) ====
        foreach (MasterData::UNITS as $name) {
            Unit::updateOrCreate(
                ['code' => MasterData::unitCode($name)],
                ['name' => $name, 'is_active' => true]
            );
        }

        // ==== Professions ====
        foreach (['Perawat', 'Dokter', 'Bidan', 'Tenaga Kefarmasian', 'Analis Laboratorium', 'Ahli Gizi', 'Petugas Kebersihan', 'Fisioterapis', 'Radiografer'] as $p) {
            Profession::updateOrCreate(['name' => $p]);
        }

        // ==== APD Types (jenis APD yang dinilai — sesuai Word 5 Okt 2026) ====
        foreach (MasterData::APD_TYPES as $name) {
            ApdType::updateOrCreate(
                ['name' => $name],
                ['description' => 'Jenis APD yang dinilai pada audit APD', 'is_active' => true]
            );
        }

        // ==== APD Actions (item tindakan pada audit APD) ====
        foreach (MasterData::APD_ACTIONS as $i => $name) {
            ApdAction::updateOrCreate(
                ['name' => $name],
                ['order' => $i + 1, 'is_active' => true]
            );
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
                'description' => 'Audit kepatuhan cuci tangan sesuai 5 Momen Kebersihan Tangan WHO.',
                'questions' => MasterData::HAND_HYGIENE_QUESTIONS,
            ],
            [
                'code' => 'apd', 'name' => 'Audit APD', 'icon' => 'bi-person-badge',
                'description' => 'Pilih tindakan yang diobservasi, lalu nilai penggunaan setiap jenis APD (Ya / Tidak).',
                'questions' => MasterData::apdQuestions(),
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
            ['name' => 'Kepala Unit IGD', 'email' => 'unit.igd@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => MasterData::unitCode('IGD P1')],
            ['name' => 'Kepala Unit ICU', 'email' => 'unit.icu@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => MasterData::unitCode('ICU 1')],
            ['name' => 'Kepala Rawat Inap', 'email' => 'unit.rin@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => MasterData::unitCode('TERATAI/GILI MOYO')],
            ['name' => 'Kepala Poli Kandungan', 'email' => 'unit.pol@ppicheck.test', 'role' => User::ROLE_UNIT, 'unit_code' => MasterData::unitCode('POLI KANDUNGAN')],
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
