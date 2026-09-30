<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('professions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pasang FK users.profession_id (tabel users dibuat lebih dulu tanpa FK untuk kompatibilitas MySQL)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('profession_id')->references('id')->on('professions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['profession_id']);
            });
        }

        Schema::dropIfExists('professions');
    }
};
