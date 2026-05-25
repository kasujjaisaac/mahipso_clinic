<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->foreignId('ordered_by')->nullable()->after('visit_id')->constrained('users')->nullOnDelete();
            $table->foreignId('resulted_by')->nullable()->after('ordered_by')->constrained('users')->nullOnDelete();
            $table->decimal('price', 12, 2)->default(0)->after('test_type');
            $table->string('result_flag')->nullable()->after('results');
            $table->boolean('is_billable')->default(true)->after('result_flag');
        });
    }

    public function down(): void
    {
        Schema::table('lab_tests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('ordered_by');
            $table->dropConstrainedForeignId('resulted_by');
            $table->dropColumn(['price', 'result_flag', 'is_billable']);
        });
    }
};
