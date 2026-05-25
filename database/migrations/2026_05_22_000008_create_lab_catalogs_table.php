<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lab_catalogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('test_name');
            $table->string('sample_type')->nullable();
            $table->string('unit')->nullable();
            $table->string('reference_range')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'test_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lab_catalogs');
    }
};
