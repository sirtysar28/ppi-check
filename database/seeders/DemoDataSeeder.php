<?php

namespace Database\Seeders;

use App\Models\ApdType;
use App\Models\Audit;
use App\Models\AuditAnswer;
use App\Models\AuditCategory;
use App\Models\AuditQuestion;
use App\Models\Finding;
use App\Models\FollowUp;
use App\Models\Profession;
use App\Models\Setting;
use App\Models\Unit;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Database\Seeder;

/**
 * Membuat data demo: audit 6 bulan terakhir, temuan, tindak lanjut,
 * dan verifikasi supaya dashboard langsung terisi.
 */
class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (Audit::count() > 0) {
            return;
        }

        $auditors   = User::where('role', User::ROLE_AUDITOR)->get();
        $units      = Unit::where('is_active', true)->get();
        $categories = AuditCategory::where('is_active', true)->with('activeQuestions')->get();
        $professions = Profession::all();
        $apdTypes   = ApdType::all();
        $unitUsers  = User::where('role', User::ROLE_UNIT)->get();

        $auditSeq   = 0;
        $findingSeq = 0;
        $year       = now()->year;

        // Untuk tiap unit: 8 audit tersebar 6 bulan terakhir dengan tren membaik
        foreach ($units as $unit) {
            $base = mt_rand(72, 85);

            for ($i = 0; $i < 8; $i++) {
                $category = $categories[$i % $categories->count()];
                $questions = $category->activeQuestions;
                if ($questions->isEmpty()) {
                    continue;
                }

                $monthsAgo = 6 - intdiv($i, 2) - 1; // bulan ke-5 (terlama) -> 0 (bulan ini)
                $date = now()->subMonths(max(0, $monthsAgo))->startOfMonth()->addDays(mt_rand(0, 26));
                if ($date->isFuture()) {
                    $date = now()->subDays(mt_rand(1, 10));
                }

                // Tren membaik dari waktu ke waktu
                $targetRate = min(98, $base + ($i * 3) + mt_rand(-5, 5));
                $auditSeq++;
                $auditNo = sprintf('PPI-%d-%04d', $year, $auditSeq);

                $answers = [];
                $conform = 0;
                $nonConform = 0;
                $naItems = 0;
                $badQuestions = collect();

                foreach ($questions as $q) {
                    $roll = mt_rand(1, 100);
                    if ($roll <= 4) {
                        $answer = 'na';
                        $naItems++;
                    } elseif ($roll <= $targetRate) {
                        $answer = 'ya';
                        $conform++;
                    } else {
                        $answer = 'tidak';
                        $nonConform++;
                        $badQuestions->push($q);
                    }

                    $answers[] = [
                        'question_id' => $q->id,
                        'answer' => $answer,
                        'score' => $answer === 'ya' ? 1 : 0,
                        'notes' => null,
                    ];
                }

                $assessed = $conform + $nonConform;
                $compliance = $assessed > 0 ? round(($conform / $assessed) * 100, 2) : 0;

                $audit = Audit::create([
                    'audit_number' => $auditNo,
                    'category_id' => $category->id,
                    'unit_id' => $unit->id,
                    'auditor_id' => $auditors->random()->id,
                    'audit_date' => $date->toDateString(),
                    'shift' => collect(['pagi', 'siang', 'malam'])->random(),
                    'officer_name' => 'Petugas ' . $unit->code . '-' . mt_rand(1, 12),
                    'profession_id' => $professions->random()->id,
                    'action_type' => $category->code === 'apd' ? collect(['Pemasangan infus', 'Perawatan luka', 'Tindakan bedah minor', 'Pengambilan darah', 'Nebulizer'])->random() : null,
                    'apd_type_id' => $category->code === 'apd' ? $apdTypes->random()->id : null,
                    'total_items' => $questions->count(),
                    'conform_items' => $conform,
                    'nonconform_items' => $nonConform,
                    'na_items' => $naItems,
                    'compliance_percentage' => $compliance,
                    'grade' => Setting::grade($compliance),
                    'status' => 'final',
                    'notes' => null,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                foreach ($answers as $a) {
                    AuditAnswer::create([
                        'audit_id' => $audit->id,
                        'question_id' => $a['question_id'],
                        'answer' => $a['answer'],
                        'score' => $a['score'],
                        'notes' => $a['notes'],
                    ]);
                }

                // Temuan untuk jawaban "tidak" (maks 2 per audit, hanya sebagian)
                $badQuestions->take(2)->each(function ($q, $idx) use ($audit, $category, $unit, &$findingSeq, $date, $unitUsers) {
                    $findingSeq++;
                    $findingNo = sprintf('TMN-%d-%04d', $date->year, $findingSeq);

                    $severity = collect(['minor', 'minor', 'mayor', 'kritis'])->random();
                    $statusPool = ['open', 'progress', 'closed'];
                    $status = $statusPool[array_rand($statusPool)];

                    $finding = Finding::create([
                        'finding_number' => $findingNo,
                        'audit_id' => $audit->id,
                        'category_id' => $category->id,
                        'question_id' => $q->id,
                        'unit_id' => $unit->id,
                        'description' => 'Tidak sesuai: ' . strtolower($q->question),
                        'location' => $unit->name,
                        'severity' => $severity,
                        'photo_path' => null,
                        'recommendation' => $this->recommendation($category->code),
                        'due_date' => $date->copy()->addDays(7)->toDateString(),
                        'status' => $status,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);

                    $unitUser = $unitUsers->firstWhere('unit_id', $unit->id);

                    if ($status !== 'open' && $unitUser) {
                        $fuDate = $date->copy()->addDays(mt_rand(1, 6));
                        $followUp = FollowUp::create([
                            'finding_id' => $finding->id,
                            'user_id' => $unitUser->id,
                            'action' => $this->followUpAction($category->code),
                            'follow_up_date' => $fuDate->toDateString(),
                            'photo_path' => null,
                            'status' => $status === 'closed' ? 'accepted' : 'submitted',
                            'created_at' => $fuDate,
                            'updated_at' => $fuDate,
                        ]);

                        if ($status === 'closed') {
                            Verification::create([
                                'finding_id' => $finding->id,
                                'verifier_id' => $audit->auditor_id,
                                'follow_up_id' => $followUp->id,
                                'verification_date' => $fuDate->copy()->addDay()->toDateString(),
                                'result' => 'accepted',
                                'notes' => 'Tindak lanjut telah sesuai. Temuan ditutup.',
                                'photo_path' => null,
                                'created_at' => $fuDate->copy()->addDay(),
                                'updated_at' => $fuDate->copy()->addDay(),
                            ]);
                        }
                    }
                });
            }
        }

        $this->command->info('Demo data: ' . Audit::count() . ' audit, ' . Finding::count() . ' temuan.');
    }

    private function recommendation(string $code): string
    {
        return match ($code) {
            'cuci-tangan' => 'Lakukan edukasi 5 momen cuci tangan dan pastikan fasilitas kebersihan tangan selalu tersedia di titik pelayanan.',
            'apd' => 'Lakukan pelatihan ulang penggunaan APD yang sesuai jenis tindakan dan pastikan ketersediaan APD di unit.',
            'sampah' => 'Melakukan pemisahan limbah infeksius dan non-infeksius sesuai tempat/warna yang ditetapkan serta lengkapi label.',
            default => 'Lakukan perbaikan sesuai standar PPI.',
        };
    }

    private function followUpAction(string $code): string
    {
        return match ($code) {
            'cuci-tangan' => 'Telah dilakukan demonstrasi cuci tangan 6 langkah kepada seluruh petugas jaga dan pengadaan handrub tambahan di ruang perawatan.',
            'apd' => 'Telah diadakan briefing penggunaan APD yang benar dan pengadaan APD sesuai jenis tindakan di unit.',
            'sampah' => 'Telah dilakukan pemilahan ulang limbah, penggantian label tempat sampah, dan pengadaan safety box baru.',
            default => 'Perbaikan telah dilaksanakan oleh unit.',
        };
    }
}
