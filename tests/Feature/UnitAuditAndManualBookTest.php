<?php

namespace Tests\Feature;

use App\Models\AuditCategory;
use App\Models\Unit;
use App\Models\User;
use Tests\TestCase;

/**
 * Fitur baru Okt 2026:
 * 1. Role Unit dapat melaksanakan audit mandiri Cuci Tangan & APD
 *    (terkunci pada unitnya sendiri, tidak boleh kategori lain).
 * 2. Buku Manual (HTML) dengan isi berbeda untuk Admin / Auditor / Unit.
 */
class UnitAuditAndManualBookTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
        $this->artisan('db:seed', ['--class' => 'MasterSeeder']);
    }

    private function unitUser(): User
    {
        return User::where('role', User::ROLE_UNIT)->firstOrFail();
    }

    public function test_unit_dapat_melihat_menu_audit_cuci_tangan_dan_apd(): void
    {
        $response = $this->actingAs($this->unitUser())->get('/dashboard');
        $response->assertOk();
        $response->assertSee('Audit Cuci Tangan');
        $response->assertSee('Audit APD');
        $response->assertSee('Buku Manual');
    }

    public function test_unit_bisa_membuka_form_audit_cuci_tangan_dan_apd(): void
    {
        $this->actingAs($this->unitUser())->get('/audits/create?category=cuci-tangan')->assertOk();
        $this->actingAs($this->unitUser())->get('/audits/create?category=apd')->assertOk();
    }

    public function test_unit_tidak_bisa_membuka_form_audit_limbah_tajam(): void
    {
        $this->actingAs($this->unitUser())->get('/audits/create?category=sampah')->assertForbidden();
    }

    public function test_unit_menyimpan_audit_apd_terkunci_pada_unitnya_sendiri(): void
    {
        $unit = $this->unitUser();
        $category = AuditCategory::where('code', 'apd')->firstOrFail();
        $otherUnit = Unit::where('id', '!=', $unit->unit_id)->firstOrFail();

        $answers = [];
        foreach ($category->activeQuestions as $q) {
            $answers[$q->id] = 'ya';
        }

        $response = $this->actingAs($unit)->post('/audits', [
            'category_id' => $category->id,
            'unit_id' => $otherUnit->id, // dicoba menyuangkan ke unit lain
            'auditor_id' => $unit->id,
            'audit_date' => now()->toDateString(),
            'shift' => 'pagi',
            'action_type' => 'Memasang infus',
            'answers' => $answers,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('audits', [
            'category_id' => $category->id,
            'unit_id' => $unit->unit_id, // dipaksa ke unit milik unit user
            'auditor_id' => $unit->id,   // auditor = dirinya sendiri
        ]);
    }

    public function test_unit_menyimpan_audit_cuci_tangan_sebagai_audit_mandiri(): void
    {
        $unit = $this->unitUser();
        $category = AuditCategory::where('code', 'cuci-tangan')->firstOrFail();

        $response = $this->actingAs($unit)->post('/audits', [
            'category_id' => $category->id,
            'unit_id' => $unit->unit_id,
            'auditor_id' => $unit->id,
            'audit_date' => now()->toDateString(),
            'shift' => 'pagi',
            'observations' => [
                1 => ['moment' => 'seb_pasien', 'action' => 'hr'],
                2 => ['moment' => 'seb_aseptik', 'action' => 'tidak'],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('audits', [
            'category_id' => $category->id,
            'auditor_id' => $unit->id,
            'compliance_percentage' => 50,
        ]);
    }

    public function test_buku_manual_berbeda_per_role(): void
    {
        $admin = User::where('role', User::ROLE_SUPER_ADMIN)->firstOrFail();
        $auditor = User::where('role', User::ROLE_AUDITOR)->firstOrFail();
        $unit = User::where('role', User::ROLE_UNIT)->firstOrFail();

        // Admin: manual lengkap semua fitur
        $this->actingAs($admin)->get('/manual-book')
            ->assertOk()
            ->assertSee('Buku Manual Admin')
            ->assertSee('Master Data')
            ->assertSee('Manajemen User')
            ->assertSee('Pengaturan Aplikasi');

        // Auditor: manual khusus auditor (tidak ada bab master data)
        $this->actingAs($auditor)->get('/manual-book')
            ->assertOk()
            ->assertSee('Buku Manual Auditor')
            ->assertSee('Memverifikasi Tindak Lanjut')
            ->assertDontSee('Manajemen User');

        // Unit: manual khusus unit (ada audit mandiri, tanpa bab verifikasi/master data)
        $this->actingAs($unit)->get('/manual-book')
            ->assertOk()
            ->assertSee('Buku Manual Unit')
            ->assertSee('Audit Mandiri Cuci Tangan')
            ->assertSee('Mengirim Tindak Lanjut Temuan')
            ->assertDontSee('Memverifikasi Tindak Lanjut Unit (khusus Auditor)')
            ->assertDontSee('Master Data');
    }

    public function test_tamu_tidak_bisa_membuka_buku_manual(): void
    {
        $this->get('/manual-book')->assertRedirect('/login');
    }
}
