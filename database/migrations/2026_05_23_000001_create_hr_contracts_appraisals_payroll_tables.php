<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->string('contract_no')->unique();
            $table->enum('contract_type', ['permanent', 'fixed_term', 'part_time', 'volunteer', 'consultant'])->default('fixed_term');
            $table->string('job_title')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('salary_amount', 12, 2)->default(0);
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('draft');
            $table->text('terms')->nullable();
            $table->date('signed_at')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'status']);
            $table->index(['employee_id', 'status']);
        });

        Schema::create('staff_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('score', 5, 2)->nullable();
            $table->string('rating')->nullable();
            $table->text('strengths')->nullable();
            $table->text('improvement_areas')->nullable();
            $table->text('goals')->nullable();
            $table->enum('status', ['draft', 'completed', 'acknowledged'])->default('draft');
            $table->date('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'status']);
            $table->index(['employee_id', 'period_end']);
        });

        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->date('period_month');
            $table->enum('status', ['draft', 'approved', 'paid', 'cancelled'])->default('draft');
            $table->decimal('gross_total', 12, 2)->default(0);
            $table->decimal('deductions_total', 12, 2)->default(0);
            $table->decimal('net_total', 12, 2)->default(0);
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'period_month']);
            $table->index(['branch_id', 'status']);
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained('payroll_runs')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('basic_pay', 12, 2)->default(0);
            $table->decimal('allowances', 12, 2)->default(0);
            $table->decimal('deductions', 12, 2)->default(0);
            $table->decimal('net_pay', 12, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['payroll_run_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
        Schema::dropIfExists('staff_appraisals');
        Schema::dropIfExists('employee_contracts');
    }
};
