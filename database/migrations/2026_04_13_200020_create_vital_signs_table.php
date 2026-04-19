<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('visit_id')->constrained()->onDelete('cascade');
            $table->float('weight')->nullable();
            $table->float('height')->nullable();
            $table->float('temperature')->nullable();
            $table->float('blood_pressure_systolic')->nullable();
            $table->float('blood_pressure_diastolic')->nullable();
            $table->float('pulse')->nullable();
            $table->float('respiratory_rate')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('vital_signs');
    }
};
