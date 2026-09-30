<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique(); // cuci-tangan | apd | sampah
            $table->string('name');
            $table->string('icon', 60)->default('bi-clipboard-check');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('audit_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('audit_categories')->cascadeOnDelete();
            $table->text('question');
            $table->decimal('weight', 5, 2)->default(1);
            $table->unsignedInteger('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_questions');
        Schema::dropIfExists('audit_categories');
    }
};
