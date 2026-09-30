<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->string('audit_number', 40)->unique(); // PPI-2026-0001
            $table->foreignId('category_id')->constrained('audit_categories')->restrictOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->foreignId('auditor_id')->constrained('users')->restrictOnDelete();
            $table->date('audit_date');
            $table->string('shift', 20)->default('pagi'); // pagi | siang | malam
            $table->string('officer_name')->nullable();   // nama petugas yang diaudit
            $table->foreignId('profession_id')->nullable()->constrained('professions')->nullOnDelete();
            $table->string('action_type')->nullable();     // jenis tindakan (audit APD)
            $table->foreignId('apd_type_id')->nullable()->constrained('apd_types')->nullOnDelete();
            $table->foreignId('waste_type_id')->nullable()->constrained('waste_types')->nullOnDelete();
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('conform_items')->default(0);
            $table->unsignedInteger('nonconform_items')->default(0);
            $table->unsignedInteger('na_items')->default(0);
            $table->decimal('compliance_percentage', 5, 2)->default(0);
            $table->string('grade', 30)->nullable(); // Sangat Baik | Baik | Cukup | Perlu Perbaikan
            $table->string('status', 20)->default('final'); // draft | final
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['audit_date']);
            $table->index(['category_id', 'unit_id']);
        });

        Schema::create('audit_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_id')->constrained('audits')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('audit_questions')->restrictOnDelete();
            $table->string('answer', 10); // ya | tidak | na
            $table->decimal('score', 5, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['audit_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_answers');
        Schema::dropIfExists('audits');
    }
};
