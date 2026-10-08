<?php

namespace Tests\Feature;

use App\Models\ApdAction;
use App\Models\ApdType;
use App\Models\SharpWasteMonitoring;
use App\Models\Unit;
use App\Models\User;
use Tests\TestCase;

class UpdateOkt2026Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');
    }

    public function test_halaman_baru_dan_data_baru_tersedia(): void
    {
        $admin = User::where('email', 'superadmin@ppicheck.test')->firstOrFail();

        // Tepat 44 ruangan dari Word (tidak ada unit lama tersisa, aktif maupun nonaktif)
        $this->assertSame(44, Unit::count());
        $this->assertSame(44, Unit::where('is_active', true)->count());

        // 19 tindakan APD
        $this->assertSame(19, ApdAction::count());

        $pages = [
            '/dashboard' => 'PPI Check',
            '/audits/create?category=apd' => 'Tindakan yang Diobservasi',
            '/audits/create?category=cuci-tangan' => 'Observasi Peluang Cuci Tangan',
            '/monitoring-limbah-tajam' => 'Lembar Monitoring Penanganan Limbah Benda Tajam',
            '/monitoring-limbah-tajam/riwayat' => 'Data Monitoring',
            '/settings' => 'Konfigurasi SMTP',
            '/master/apd-actions' => 'Daftar Tindakan',
            '/master/instruments' => 'Instrumen',
            '/master/instruments/2' => 'Penggunaan',
            '/audits' => 'Riwayat',
            '/findings' => 'Monitoring Temuan',
            '/follow-ups' => 'Tindak',
            '/profile' => 'Profil',
        ];

        foreach ($pages as $uri => $expect) {
            $res = $this->actingAs($admin)->get($uri);
            $res->assertOk();
            $this->assertStringContainsString($expect, $res->getContent(), "Halaman {$uri}");
        }

        // Header & footer baru
        $dashboard = $this->actingAs($admin)->get('/dashboard')->getContent();
        $this->assertStringContainsString('bi-bell', $dashboard, 'Ikon notifikasi tampil');
        $this->assertStringContainsString("family=Inter", $dashboard, 'Font Inter dimuat');
        $this->assertStringContainsString('Powered by', $dashboard, 'Footer baru');

        // Kepatuhan per Unit: terpaginasi 10 unit/halaman (total 44 unit)
        $this->assertStringContainsString('44 unit', $dashboard, 'Badge total unit');
        $this->assertStringContainsString('Menampilkan', $dashboard, 'Info rentang halaman');
        $this->assertStringContainsString('pagination', $dashboard, 'Navigasi pagination tampil');
        $this->assertSame(10, substr_count($dashboard, 'dashboard/unit/'), 'Hanya 10 baris unit per halaman');

        // Halaman 2 + filter tetap jalan
        $page2 = $this->actingAs($admin)->get('/dashboard?page=2&start=2020-01-01');
        $page2->assertOk();
        $this->assertStringContainsString('Menampilkan 11–20 dari 44 unit', $page2->getContent());
    }

    public function test_migrate_tanpa_seed_tetap_menghasilkan_data_master(): void
    {
        // Simulasi deployment production: hanya migrate, tanpa db:seed
        $this->artisan('migrate:fresh');

        $this->assertSame(44, Unit::count());
        $this->assertSame(19, ApdAction::count());
        $this->assertSame(
            ['Apron', 'Goggle', 'Masker', 'Sarung Tangan', 'Sepatu Boot', 'Tutup Kepala'],
            ApdType::orderBy('name')->pluck('name')->values()->all()
        );
        $this->assertSame(5, \App\Models\AuditCategory::where('code', 'cuci-tangan')->firstOrFail()->activeQuestions()->count());
    }

    public function test_simpan_monitoring_limbah_tajam(): void
    {
        $admin = User::where('email', 'superadmin@ppicheck.test')->firstOrFail();
        $unit = Unit::where('is_active', true)->firstOrFail();

        $statements = SharpWasteMonitoring::STATEMENTS;
        $items = [];
        foreach ($statements as $i => $s) {
            $items[$i] = [
                'pernyataan' => $s,
                'jawaban' => $i < 6 ? 'Ya' : 'Tidak', // 6 ya, 2 tidak = 75%
                'keterangan' => $i === 7 ? 'Ditemukan ketidaksesuaian' : '',
            ];
        }

        $res = $this->actingAs($admin)->post('/monitoring-limbah-tajam', [
            'monitoring_date' => now()->toDateString(),
            'unit_id' => $unit->id,
            'officer_name' => 'Petugas Uji',
            'notes' => 'Catatan uji',
            'items' => $items,
        ]);

        $res->assertRedirect();

        $m = SharpWasteMonitoring::latest('id')->first();
        $this->assertSame(6, $m->conform_items);
        $this->assertSame(2, $m->nonconform_items);
        $this->assertEquals(75.0, (float) $m->compliance_percentage);
        $this->assertSame('Cukup', $m->grade);
        $this->assertSame(8, $m->items()->count());

        // Detail & riwayat tampil
        $this->actingAs($admin)->get("/monitoring-limbah-tajam/{$m->id}")->assertOk();
        $this->actingAs($admin)->get('/monitoring-limbah-tajam/riwayat')->assertOk();
    }

    public function test_simpan_audit_apd_dengan_tindakan_baru(): void
    {
        $admin = User::where('email', 'superadmin@ppicheck.test')->firstOrFail();
        $unit = Unit::where('is_active', true)->firstOrFail();
        $category = \App\Models\AuditCategory::where('code', 'apd')->firstOrFail();
        $questions = $category->activeQuestions()->pluck('id');

        $answers = [];
        foreach ($questions as $qid) {
            $answers[$qid] = $qid === $questions->first() ? 'tidak' : 'ya';
        }
        $firstQid = $questions->first();

        $res = $this->actingAs($admin)->post('/audits', [
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'auditor_id' => $admin->id,
            'audit_date' => now()->toDateString(),
            'shift' => 'pagi',
            'officer_name' => 'Perawat Uji',
            'action_type' => 'Memasang Infus',
            'answers' => $answers,
            'findings' => [
                $firstQid => [
                    'question_id' => $firstQid,
                    'description' => 'Sarung tangan tidak digunakan',
                    'severity' => 'minor',
                ],
            ],
        ]);

        $res->assertRedirect();

        $audit = \App\Models\Audit::latest('id')->first();
        $this->assertSame('Memasang Infus', $audit->action_type);
        $this->assertSame(5, $audit->conform_items); // 5 dari 6 APD
        $this->assertEquals(83.33, (float) $audit->compliance_percentage);
        $this->assertSame(1, $audit->findings()->count());

        // Audit tanpa tindakan harus ditolak
        $res2 = $this->actingAs($admin)->post('/audits', [
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'auditor_id' => $admin->id,
            'audit_date' => now()->toDateString(),
            'shift' => 'pagi',
            'action_type' => null,
            'answers' => $answers,
        ]);
        $res2->assertSessionHasErrors('action_type');
    }

    public function test_setting_smtp_logo_password(): void
    {
        $admin = User::where('email', 'superadmin@ppicheck.test')->firstOrFail();

        // SMTP tersimpan
        $this->actingAs($admin)->put('/settings/smtp', [
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.gmail.com',
            'mail_port' => 587,
            'mail_encryption' => 'tls',
            'mail_username' => 'ppi@rs.co.id',
            'mail_password' => '',
            'mail_from_address' => 'ppi@rs.co.id',
            'mail_from_name' => 'PPI Check',
        ])->assertRedirect();

        $this->assertSame('smtp.gmail.com', \App\Models\Setting::get('mail_host'));

        // Ganti password
        $this->actingAs($admin)->put('/settings/password', [
            'current_password' => 'password',
            'new_password' => 'rahasiaBaru123',
            'new_password_confirmation' => 'rahasiaBaru123',
        ])->assertRedirect();

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('rahasiaBaru123', $admin->fresh()->password));

        // Password lama salah
        $this->actingAs($admin)->put('/settings/password', [
            'current_password' => 'salah',
            'new_password' => 'lain12345',
            'new_password_confirmation' => 'lain12345',
        ])->assertSessionHasErrors('current_password');

        // Kembalikan password demo
        $admin->fresh()->update(['password' => bcrypt('password')]);
    }
}
