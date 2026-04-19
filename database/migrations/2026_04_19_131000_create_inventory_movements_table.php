<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventory_id');
            $table->string('action');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->integer('quantity')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->dateTime('performed_at')->nullable();
            $table->timestamps();

            $table->foreign('inventory_id')->references('id')->on('inventories')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
