<?php

namespace Tests\Feature;

use App\Models\Audit;
use App\Models\AuditCategory;
use App\Models\HandHygieneObservation;
use App\Models\Unit;
use App\Models\User;
use Tests\TestCase;

/**
 * Lembar Audit Cuci Tangan (format Word tgl 8 Okt 2026):
 * identitas (observer, ruang, bulan/tanggal) + maks 24 observasi
 * yang tiap barisnya memilih momen (5 Momen WHO) dan tindakan
 * (HR / HW / Tidak / Set lepas sarung tangan).
 */
class HandHygieneAuditTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate:fresh');
        $this->artisan('db:seed');
    }

    private function auditor(): User
    {
        return User::where('role', 'auditor')->where('is_active', true)->firstOrFail();
    }

    private function categoryId(): int
    {
        return AuditCategory::where('code', 'cuci-tangan')->firstOrFail()->id;
    }

    public function test_form_audit_cuci_tangan_menampilkan_24_observasi(): void
    {
        $res = $this->actingAs($this->auditor())->get('/audits/create?category=cuci-tangan');

        $res->assertOk();
        $html = $res->getContent();
        $this->assertStringContainsString('Observasi Peluang Cuci Tangan', $html);
        $this->assertStringContainsString('Nama Observer', $html);
        // 24 baris observasi (radio momen baris ke-24)
        $this->assertStringContainsString('name="observations[24][moment]"', $html);
        $this->assertStringContainsString('name="observations[24][action]"', $html);
        $this->assertStringContainsString('Set. lepas sarung tangan', $html);
    }

    public function test_simpan_audit_dan_hitung_kepatuhan_otomatis(): void
    {
        $auditor = $this->auditor();

        $res = $this->actingAs($auditor)->post('/audits', [
            'category_id' => $this->categoryId(),
            'unit_id' => Unit::first()->id,
            'auditor_id' => $auditor->id,
            'audit_date' => '2026-10-08',
            'shift' => 'pagi',
            'observations' => [
                1 => ['moment' => 'seb_pasien', 'action' => 'hr'],
                2 => ['moment' => 'seb_aseptik', 'action' => 'hw'],
                3 => ['moment' => 'set_pasien', 'action' => 'tidak'],
                4 => ['moment' => 'set_lingkungan', 'action' => 'set_lepas_sarung_tangan'],
            ],
            'notes' => 'Uji coba',
        ]);

        $audit = Audit::where('notes', 'Uji coba')->firstOrFail();
        $res->assertRedirect(route('audits.show', $audit));

        // 4 observasi tersimpan, 3 patuh (HR, HW, set lepas sarung tangan), 1 tidak
        $this->assertSame(4, $audit->observations()->count());
        $this->assertSame(4, $audit->total_items);
        $this->assertSame(3, $audit->conform_items);
        $this->assertSame(1, $audit->nonconform_items);
        $this->assertSame(75.0, (float) $audit->compliance_percentage);

        // Halaman hasil menampilkan detail observasi
        $show = $this->actingAs($auditor)->get(route('audits.show', $audit));
        $show->assertOk();
        $this->assertStringContainsString('Detail Observasi Cuci Tangan', $show->getContent());
        $this->assertStringContainsString('Sebelum kontak dengan pasien', $show->getContent());

        // PDF lembar audit
        $pdf = $this->actingAs($auditor)->get(route('audits.pdf', $audit));
        $pdf->assertOk();
        $pdf->assertHeader('content-type', 'application/pdf');
    }

    public function test_validasi_baris_setengah_terisi_ditolak(): void
    {
        $auditor = $this->auditor();
        $before = Audit::count();

        $res = $this->actingAs($auditor)->post('/audits', [
            'category_id' => $this->categoryId(),
            'unit_id' => Unit::first()->id,
            'auditor_id' => $auditor->id,
            'audit_date' => '2026-10-08',
            'shift' => 'pagi',
            'observations' => [
                1 => ['moment' => 'seb_pasien', 'action' => null], // setengah terisi
            ],
        ]);

        $res->assertSessionHasErrors('observations');
        $this->assertSame($before, Audit::count());
    }

    public function test_minimal_satu_observasi_wajib(): void
    {
        $auditor = $this->auditor();
        $before = Audit::count();

        $res = $this->actingAs($auditor)->from('/audits/create?category=cuci-tangan')->post('/audits', [
            'category_id' => $this->categoryId(),
            'unit_id' => Unit::first()->id,
            'auditor_id' => $auditor->id,
            'audit_date' => '2026-10-08',
            'shift' => 'pagi',
            'observations' => [],
        ]);

        $res->assertSessionHasErrors('observations');
        $this->assertSame($before, Audit::count());
    }
}
