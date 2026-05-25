<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'description')) {
                $table->string('description')->nullable()->after('guard_name');
            }

            if (! Schema::hasColumn('roles', 'module_access')) {
                $table->json('module_access')->nullable()->after('description');
            }
        });

        Schema::table('employees', function (Blueprint $table) {
            if (! Schema::hasColumn('employees', 'role_name')) {
                $table->string('role_name')->nullable()->after('job_title')->index();
            }
        });

        $defaults = config('clinic_modules.default_access', []);

        foreach ($defaults as $roleName => $modules) {
            DB::table('roles')
                ->where('name', $roleName)
                ->whereNull('module_access')
                ->update(['module_access' => json_encode(array_values($modules))]);
        }
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (Schema::hasColumn('employees', 'role_name')) {
                $table->dropColumn('role_name');
            }
        });

        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'module_access')) {
                $table->dropColumn('module_access');
            }

            if (Schema::hasColumn('roles', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
