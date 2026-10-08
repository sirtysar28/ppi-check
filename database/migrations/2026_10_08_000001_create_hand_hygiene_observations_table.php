<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Observasi lembar audit cuci tangan (sesuai "LEMBAR AUDIT CUCI TANGAN" tgl 8 Okt 2026):
 * 24 peluang observasi, tiap baris berisi momen (5 Momen WHO) + tindakan
 * (HR / HW / Tidak / Set lepas sarung tangan).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hand_hygiene_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('sequence'); // 1..24
            $table->string('moment', 40);            // seb_pasien | seb_aseptik | set_cairan_tubuh | set_pasien | set_lingkungan
            $table->string('action', 40);            // hr | hw | tidak | set_lepas_sarung_tangan
            $table->timestamps();

            $table->unique(['audit_id', 'sequence']);
            $table->index(['moment', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hand_hygiene_observations');
    }
};
