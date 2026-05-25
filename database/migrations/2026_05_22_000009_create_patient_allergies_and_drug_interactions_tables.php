<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('patient_allergies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->string('substance');
            $table->string('reaction')->nullable();
            $table->enum('severity', ['mild', 'moderate', 'severe', 'unknown'])->default('unknown');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('drug_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('drug_a');
            $table->string('drug_b');
            $table->enum('severity', ['moderate', 'major', 'contraindicated'])->default('moderate');
            $table->text('warning')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drug_interactions');
        Schema::dropIfExists('patient_allergies');
    }
};
