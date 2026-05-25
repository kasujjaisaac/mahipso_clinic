<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('failed_login_count')->default(0)->after('remember_token');
            $table->timestamp('locked_until')->nullable()->after('failed_login_count');
            $table->timestamp('last_password_changed_at')->nullable()->after('locked_until');
            $table->boolean('must_change_password')->default(false)->after('last_password_changed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['failed_login_count', 'locked_until', 'last_password_changed_at', 'must_change_password']);
        });
    }
};
