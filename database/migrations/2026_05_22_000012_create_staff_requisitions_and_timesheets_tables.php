<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->after('branch_id');
            $table->string('job_title')->nullable()->after('employee_number');
            $table->string('department')->nullable()->after('job_title');
            $table->foreignId('line_supervisor_id')->nullable()->after('department')->constrained('users')->nullOnDelete();
        });

        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('line_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('serial_number')->unique();
            $table->string('department')->nullable();
            $table->date('requested_at');
            $table->enum('status', ['draft', 'submitted', 'supervisor_approved', 'changes_requested', 'rejected', 'finance_checked', 'approved'])->default('draft');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('amount_in_words')->nullable();
            $table->text('purpose')->nullable();
            $table->text('supervisor_comments')->nullable();
            $table->timestamp('supervisor_reviewed_at')->nullable();
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('finance_comments')->nullable();
            $table->timestamps();
            $table->index(['branch_id', 'status']);
            $table->index(['requested_by', 'status']);
            $table->index(['line_supervisor_id', 'status']);
        });

        Schema::create('requisition_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requisition_id')->constrained('requisitions')->cascadeOnDelete();
            $table->string('item');
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('quantity', 10, 2)->default(1);
            $table->string('frequency')->nullable();
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('monthly_timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('line_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_number')->nullable();
            $table->string('job_title')->nullable();
            $table->date('month');
            $table->date('prepared_at')->nullable();
            $table->enum('status', ['draft', 'submitted', 'supervisor_approved', 'changes_requested', 'rejected', 'hr_received'])->default('draft');
            $table->decimal('total_hours', 8, 2)->default(0);
            $table->text('staff_comments')->nullable();
            $table->text('supervisor_comments')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('supervisor_reviewed_at')->nullable();
            $table->foreignId('hr_received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_received_at')->nullable();
            $table->text('hr_comments')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'month']);
            $table->index(['branch_id', 'status']);
            $table->index(['line_supervisor_id', 'status']);
        });

        Schema::create('monthly_timesheet_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('monthly_timesheet_id')->constrained('monthly_timesheets')->cascadeOnDelete();
            $table->unsignedTinyInteger('day');
            $table->text('work_specification')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_finish')->nullable();
            $table->decimal('hours', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['monthly_timesheet_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_timesheet_entries');
        Schema::dropIfExists('monthly_timesheets');
        Schema::dropIfExists('requisition_items');
        Schema::dropIfExists('requisitions');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('line_supervisor_id');
            $table->dropColumn(['employee_number', 'job_title', 'department']);
        });
    }
};
