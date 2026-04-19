<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->string('status')->default('in_store')->after('quantity');
            $table->string('location')->nullable()->after('status');
            $table->unsignedBigInteger('assigned_to')->nullable()->after('location');
            $table->dateTime('assigned_at')->nullable()->after('assigned_to');
            $table->unsignedBigInteger('disposed_by')->nullable()->after('assigned_at');
            $table->dateTime('disposed_at')->nullable()->after('disposed_by');
            $table->text('disposal_reason')->nullable()->after('disposed_at');
            $table->date('purchase_date')->nullable()->after('unit_price');

            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('disposed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['disposed_by']);
            $table->dropColumn([
                'status',
                'location',
                'assigned_to',
                'assigned_at',
                'disposed_by',
                'disposed_at',
                'disposal_reason',
                'purchase_date',
            ]);
        });
    }
};
