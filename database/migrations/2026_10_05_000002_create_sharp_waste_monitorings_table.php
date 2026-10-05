<?php

use Database\Seeders\UpdateOkt2026Seeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sharp_waste_monitorings', function (Blueprint $table) {
            $table->id();
            $table->string('monitoring_number', 40)->unique(); // LBT-2026-0001
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('officer_name');                    // petugas yang dimonitor
            $table->date('monitoring_date');
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('conform_items')->default(0);    // jumlah jawaban Ya
            $table->unsignedInteger('nonconform_items')->default(0); // jumlah jawaban Tidak
            $table->decimal('compliance_percentage', 5, 2)->default(0);
            $table->string('grade', 30)->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // petugas input
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['monitoring_date']);
        });

        Schema::create('sharp_waste_monitoring_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monitoring_id')->constrained('sharp_waste_monitorings')->cascadeOnDelete();
            $table->text('statement');          // pernyataan
            $table->string('answer', 10);      // ya | tidak
            $table->text('notes')->nullable();  // keterangan
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();
        });

        // Sinkronisasi data master sesuai dokumen "Update tgl 5 Okt 2026"
        // (44 ruangan, item cuci tangan, item APD + tindakan APD).
        // Dipanggil dari migrasi agar deployment cukup `php artisan migrate --force`.
        // Seeder ini idempotent — aman dijalankan berulang.
        (new UpdateOkt2026Seeder())->run();
    }

    public function down(): void
    {
        Schema::dropIfExists('sharp_waste_monitoring_items');
        Schema::dropIfExists('sharp_waste_monitorings');
    }
};
