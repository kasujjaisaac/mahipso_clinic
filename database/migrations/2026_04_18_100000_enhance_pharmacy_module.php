<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create Product Categories table (if it doesn't exist)
        if (!Schema::hasTable('product_categories')) {
            Schema::create('product_categories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pharmacy_id')->constrained()->onDelete('cascade');
                $table->string('name');
                $table->text('description')->nullable();
                $table->timestamps();
                $table->unique(['pharmacy_id', 'name']);
            });
        }

        // Create Product Audit Logs table (if it doesn't exist)
        if (!Schema::hasTable('product_audit_logs')) {
            Schema::create('product_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('action');
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('reason')->nullable();
                $table->timestamps();
                $table->index('product_id');
                $table->index('created_at');
            });
        }

        // Enhance products table
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'product_category_id')) {
                $table->foreignId('product_category_id')->nullable()->after('pharmacy_id')
                    ->constrained('product_categories')->nullOnDelete();
            }
            if (!Schema::hasColumn('products', 'status')) {
                $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active')->after('minimum_stock');
            }
            if (!Schema::hasColumn('products', 'expires_soon_notified_at')) {
                $table->timestamp('expires_soon_notified_at')->nullable()->after('status');
            }
        });

        // Enhance sales table
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'status')) {
                $table->enum('status', ['completed', 'voided', 'refunded'])->default('completed')->after('sale_date');
            }
            if (!Schema::hasColumn('sales', 'void_reason')) {
                $table->string('void_reason')->nullable()->after('status');
            }
            if (!Schema::hasColumn('sales', 'voided_by')) {
                $table->foreignId('voided_by')->nullable()->after('void_reason')
                    ->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('sales', 'voided_at')) {
                $table->timestamp('voided_at')->nullable()->after('voided_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['status', 'void_reason', 'voided_by', 'voided_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeignKey(['product_category_id']);
            $table->dropColumn(['product_category_id', 'status', 'expires_soon_notified_at']);
        });

        Schema::dropIfExists('product_audit_logs');
        Schema::dropIfExists('product_categories');
    }
};
