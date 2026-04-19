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
        Schema::table('audit_logs', function (Blueprint $table) {
            // Login/Logout specific tracking
            $table->enum('action_type', ['login', 'logout', 'access', 'create', 'update', 'delete', 'view'])->nullable()->after('action');
            $table->timestamp('login_time')->nullable()->after('action_type');
            $table->timestamp('logout_time')->nullable()->after('login_time');
            $table->string('login_status')->nullable()->comment('success, failed, session_expired')->after('logout_time');
            $table->integer('session_duration_minutes')->nullable()->after('login_status');

            // Device and Browser tracking
            $table->string('browser')->nullable()->after('user_agent');
            $table->string('browser_version')->nullable()->after('browser');
            $table->string('operating_system')->nullable()->after('browser_version');
            $table->string('device_type')->nullable()->after('operating_system');

            // Change tracking
            $table->json('old_values')->nullable()->comment('Previous values before change')->after('device_type');
            $table->json('new_values')->nullable()->comment('New values after change')->after('old_values');
            $table->text('changes_summary')->nullable()->after('new_values');

            // Enhanced tracking
            $table->string('resource_type')->nullable()->comment('e.g., Patient, Appointment, Bill')->after('changes_summary');
            $table->unsignedBigInteger('resource_id')->nullable()->comment('ID of affected resource')->after('resource_type');
            $table->string('status')->default('completed')->comment('completed, pending, failed')->after('resource_id');
            $table->text('error_message')->nullable()->after('status');

            // Indexes for performance
            $table->index('user_id');
            $table->index('module');
            $table->index('action_type');
            $table->index('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['module', 'action_type', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'created_at']);
            $table->dropIndex(['module', 'action_type', 'created_at']);
            $table->dropIndex('audit_logs_user_id_index');
            $table->dropIndex('audit_logs_module_index');
            $table->dropIndex('audit_logs_action_type_index');
            $table->dropIndex('audit_logs_created_at_index');

            $table->dropColumn([
                'action_type',
                'login_time',
                'logout_time',
                'login_status',
                'session_duration_minutes',
                'browser',
                'browser_version',
                'operating_system',
                'device_type',
                'old_values',
                'new_values',
                'changes_summary',
                'resource_type',
                'resource_id',
                'status',
                'error_message',
            ]);
        });
    }
};
