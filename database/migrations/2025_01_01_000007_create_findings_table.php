<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('findings', function (Blueprint $table) {
            $table->id();
            $table->string('finding_number', 40)->unique(); // TMN-2026-0001
            $table->foreignId('audit_id')->constrained('audits')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('audit_categories')->restrictOnDelete();
            $table->foreignId('question_id')->nullable()->constrained('audit_questions')->nullOnDelete();
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->text('description');
            $table->string('location')->nullable();
            $table->string('severity', 20)->default('minor'); // minor | mayor | kritis
            $table->string('photo_path')->nullable();
            $table->text('recommendation')->nullable();
            $table->date('due_date')->nullable();            // batas waktu tindak lanjut
            $table->string('status', 30)->default('open');   // open | progress | closed
            $table->timestamps();

            $table->index(['status']);
            $table->index(['unit_id', 'status']);
        });

        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')->constrained('findings')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->text('action');
            $table->date('follow_up_date');
            $table->string('photo_path')->nullable();
            $table->string('status', 20)->default('submitted'); // submitted | accepted | rejected
            $table->timestamps();
        });

        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('finding_id')->constrained('findings')->cascadeOnDelete();
            $table->foreignId('verifier_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('follow_up_id')->nullable()->constrained('follow_ups')->nullOnDelete();
            $table->date('verification_date');
            $table->string('result', 20); // accepted | rejected
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verifications');
        Schema::dropIfExists('follow_ups');
        Schema::dropIfExists('findings');
    }
};
