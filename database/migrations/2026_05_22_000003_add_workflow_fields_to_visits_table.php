<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->string('workflow_stage')->default('checked_in')->after('status');
            $table->dateTime('checked_in_at')->nullable()->after('workflow_stage');
            $table->dateTime('triaged_at')->nullable()->after('checked_in_at');
            $table->dateTime('consultation_started_at')->nullable()->after('triaged_at');
            $table->dateTime('lab_started_at')->nullable()->after('consultation_started_at');
            $table->dateTime('pharmacy_started_at')->nullable()->after('lab_started_at');
            $table->dateTime('billing_started_at')->nullable()->after('pharmacy_started_at');
            $table->dateTime('completed_at')->nullable()->after('billing_started_at');
            $table->index(['branch_id', 'workflow_stage', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropIndex(['branch_id', 'workflow_stage', 'status']);
            $table->dropColumn([
                'workflow_stage',
                'checked_in_at',
                'triaged_at',
                'consultation_started_at',
                'lab_started_at',
                'pharmacy_started_at',
                'billing_started_at',
                'completed_at',
            ]);
        });
    }
};
