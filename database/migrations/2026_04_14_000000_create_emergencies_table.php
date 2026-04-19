<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('emergencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->string('type');
            $table->text('description')->nullable();
            $table->string('status'); // e.g. open, resolved, referred
            $table->timestamp('reported_at');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('emergencies');
    }
};
