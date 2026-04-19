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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->enum('category', [
                'utilities',
                'rent_lease',
                'equipment',
                'supplies',
                'salaries',
                'insurance',
                'marketing',
                'professional_fees',
                'taxes',
                'loans',
                'maintenance',
                'other'
            ]);
            $table->string('subcategory')->nullable(); // e.g., 'electricity', 'water', 'internet'
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('vendor')->nullable();
            $table->string('invoice_number')->nullable();
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'credit_card', 'debit_card'])->nullable();
            $table->date('paid_at')->nullable();
            $table->date('due_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->boolean('recurring')->default(false);
            $table->enum('frequency', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly'])->nullable();
            $table->text('notes')->nullable();
            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
