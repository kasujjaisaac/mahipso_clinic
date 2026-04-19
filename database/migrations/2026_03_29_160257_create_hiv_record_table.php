<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hiv_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained('visits')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('provider_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('test_type', ['rapid', 'elisa', 'pcr', 'viral_load', 'cd4', 'other'])->default('rapid');
            $table->enum('test_result', ['negative', 'positive', 'indeterminate', 'unknown'])->default('unknown');
            $table->integer('cd4_count')->nullable();
            $table->integer('viral_load')->nullable();
            $table->string('art_status')->nullable();
            $table->string('regimen')->nullable();
            $table->text('adherence')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hiv_records');
    }
};
