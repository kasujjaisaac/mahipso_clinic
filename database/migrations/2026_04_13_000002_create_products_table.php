<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->date('purchase_date');
            $table->date('expiry_date')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image_path')->nullable();
            $table->unsignedInteger('quantity')->default(0);
            $table->unsignedInteger('minimum_stock')->default(0);
            $table->foreignId('added_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
