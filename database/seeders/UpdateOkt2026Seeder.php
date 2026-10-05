<?php

namespace Database\Seeders;

use App\Models\ApdAction;
use App\Models\ApdType;
use App\Models\AuditCategory;
use App\Models\AuditQuestion;
use App\Models\SharpWasteMonitoring;
use App\Models\Unit;
use App\Support\MasterData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Sinkronisasi data lama dengan dokumen "Update tgl 5 Okt 2026":
 * - Ganti daftar ruangan dengan 44 ruangan dari Word
 * - Ganti item penilaian Cuci Tangan (5 momen WHO)
 * - Ganti item penilaian APD (tindakan + 6 jenis APD yang dinilai)
 *
 * Ruangan lama yang tidak ada di daftar Word DIHAPUS PERMANEN
 * beserta seluruh riwayat yang menautnya (audit, temuan, tindak lanjut,
 * verifikasi), karena audit wajib terhubung ke sebuah ruangan.
 *
 * Jalankan: php artisan db:seed --class=UpdateOkt2026Seeder
 */
class UpdateOkt2026Seeder extends Seeder
{
    public function run(): void
    {
        $this->syncUnits();
        $this->syncCategories();
        $this->syncApdTypes();
        $this->syncApdActions();
    }

    protected function syncUnits(): void
    {
        $codes = [];

        foreach (MasterData::UNITS as $i => $name) {
            $code = MasterData::unitCode($name);
            $codes[] = $code;

            Unit::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'is_active' => true]
            );
        }

        // Ruangan lama yang tidak ada di daftar Word: HAPUS permanen
        // beserta riwayat audit / temuan / tindak lanjut / verifikasinya
        // (audit wajib terhubung ke ruangan, sehingga tidak bisa dibiarkan).
        Unit::whereNotIn('code', $codes)->get()->each(function (Unit $unit) {
            DB::transaction(function () use ($unit) {
                // temuan unit lama — ikut cascade: tindak lanjut & verifikasi
                $unit->findings()->delete();

                // audit unit lama — ikut cascade: jawaban audit
                $unit->audits()->delete();

                // monitoring limbah benda tajam pada unit lama
                SharpWasteMonitoring::where('unit_id', $unit->id)->delete();

                // lepas user (cth. kepala unit lama) dari ruangan
                $unit->users()->update(['unit_id' => null]);

                $unit->delete();
            });
        });
    }

    protected function syncCategories(): void
    {
        // ---- Audit Cuci Tangan: 5 momen WHO ----
        $cuci = AuditCategory::updateOrCreate(
            ['code' => 'cuci-tangan'],
            [
                'name' => 'Audit Cuci Tangan',
                'icon' => 'bi-droplet-half',
                'description' => 'Audit kepatuhan cuci tangan sesuai 5 Momen Kebersihan Tangan WHO.',
                'is_active' => true,
            ]
        );
        $this->syncQuestions($cuci, MasterData::HAND_HYGIENE_QUESTIONS);

        // ---- Audit APD: 1 tindakan diobservasi, tiap jenis APD dinilai Ya/Tidak ----
        $apd = AuditCategory::updateOrCreate(
            ['code' => 'apd'],
            [
                'name' => 'Audit APD',
                'icon' => 'bi-person-badge',
                'description' => 'Pilih tindakan yang diobservasi, lalu nilai penggunaan setiap jenis APD (Ya / Tidak).',
                'is_active' => true,
            ]
        );
        $this->syncQuestions($apd, MasterData::apdQuestions());
    }

    protected function syncQuestions(AuditCategory $category, array $questions): void
    {
        foreach ($questions as $i => $question) {
            AuditQuestion::updateOrCreate(
                ['category_id' => $category->id, 'question' => $question],
                ['weight' => 1, 'order' => $i + 1, 'is_active' => true]
            );
        }

        // Item lama yang tidak ada di daftar baru: nonaktifkan bila sudah pernah
        // dijawab pada audit (riwayat tetap tersimpan), hapus bila belum terpakai.
        $category->questions()->whereNotIn('question', $questions)->get()->each(function (AuditQuestion $q) {
            if ($q->answers()->exists()) {
                $q->update(['is_active' => false]);
            } else {
                $q->delete();
            }
        });
    }

    protected function syncApdTypes(): void
    {
        foreach (MasterData::APD_TYPES as $name) {
            ApdType::updateOrCreate(
                ['name' => $name],
                ['description' => 'Jenis APD yang dinilai pada audit APD', 'is_active' => true]
            );
        }

        // Jenis APD lama dihapus (audit lama otomatis melepas relasinya)
        ApdType::whereNotIn('name', MasterData::APD_TYPES)->delete();
    }

    protected function syncApdActions(): void
    {
        foreach (MasterData::APD_ACTIONS as $i => $name) {
            ApdAction::updateOrCreate(
                ['name' => $name],
                ['order' => $i + 1, 'is_active' => true]
            );
        }

        ApdAction::whereNotIn('name', MasterData::APD_ACTIONS)->delete();
    }
}
