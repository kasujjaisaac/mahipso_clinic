<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (! Schema::hasColumn('sales', 'patient_id')) {
                $table->foreignId('patient_id')->nullable()->after('pharmacy_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('sales', 'visit_id')) {
                $table->foreignId('visit_id')->nullable()->after('patient_id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('sales', 'provider_id')) {
                $table->foreignId('provider_id')->nullable()->after('visit_id')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('sales', 'prescription_note')) {
                $table->text('prescription_note')->nullable()->after('voided_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'prescription_note')) {
                $table->dropColumn('prescription_note');
            }
            if (Schema::hasColumn('sales', 'provider_id')) {
                $table->dropForeign(['provider_id']);
                $table->dropColumn('provider_id');
            }
            if (Schema::hasColumn('sales', 'visit_id')) {
                $table->dropForeign(['visit_id']);
                $table->dropColumn('visit_id');
            }
            if (Schema::hasColumn('sales', 'patient_id')) {
                $table->dropForeign(['patient_id']);
                $table->dropColumn('patient_id');
            }
        });
    }
};
