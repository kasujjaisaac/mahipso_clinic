<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('type', ['medical', 'surgical', 'maternity', 'pediatric', 'icu', 'isolation', 'observation', 'other'])->default('medical');
            $table->enum('gender_restriction', ['none', 'male', 'female'])->default('none');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['branch_id', 'name']);
        });

        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained('wards')->cascadeOnDelete();
            $table->string('bed_number');
            $table->enum('status', ['available', 'occupied', 'reserved', 'cleaning', 'maintenance'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamp('last_cleaned_at')->nullable();
            $table->timestamps();
            $table->unique(['ward_id', 'bed_number']);
        });

        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->foreignId('admitting_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('current_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ward_id')->constrained('wards')->restrictOnDelete();
            $table->foreignId('bed_id')->constrained('beds')->restrictOnDelete();
            $table->string('admission_no')->unique();
            $table->enum('admission_type', ['emergency', 'elective', 'referral', 'observation', 'maternity', 'surgical', 'other'])->default('emergency');
            $table->enum('status', ['admitted', 'ready_for_discharge', 'pending_clearance', 'discharged', 'transferred', 'deceased', 'absconded'])->default('admitted');
            $table->timestamp('admitted_at');
            $table->timestamp('expected_discharge_at')->nullable();
            $table->timestamp('discharge_started_at')->nullable();
            $table->timestamp('discharged_at')->nullable();
            $table->string('discharge_type')->nullable();
            $table->text('reason_for_admission');
            $table->text('provisional_diagnosis')->nullable();
            $table->text('current_diagnosis')->nullable();
            $table->text('care_plan')->nullable();
            $table->string('payment_type')->nullable();
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->text('consent_notes')->nullable();
            $table->boolean('nursing_cleared')->default(false);
            $table->boolean('pharmacy_cleared')->default(false);
            $table->boolean('billing_cleared')->default(false);
            $table->timestamps();
            $table->index(['branch_id', 'status']);
            $table->index(['patient_id', 'status']);
        });

        Schema::create('inpatient_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('note_type', ['doctor_round', 'nursing', 'care_plan', 'handover', 'procedure', 'other'])->default('other');
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        Schema::create('inpatient_vitals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->unsignedSmallInteger('blood_pressure_systolic')->nullable();
            $table->unsignedSmallInteger('blood_pressure_diastolic')->nullable();
            $table->unsignedSmallInteger('pulse')->nullable();
            $table->unsignedSmallInteger('respiratory_rate')->nullable();
            $table->unsignedSmallInteger('oxygen_saturation')->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->decimal('intake_ml', 8, 2)->nullable();
            $table->decimal('output_ml', 8, 2)->nullable();
            $table->unsignedTinyInteger('pain_score')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('recorded_at');
            $table->timestamps();
        });

        Schema::create('medication_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('prescribed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('medicine_name');
            $table->string('dose');
            $table->string('route')->nullable();
            $table->string('frequency')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('stop_at')->nullable();
            $table->enum('status', ['active', 'paused', 'stopped', 'completed'])->default('active');
            $table->text('instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('medication_administrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_order_id')->constrained('medication_orders')->cascadeOnDelete();
            $table->foreignId('administered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('administered_at')->nullable();
            $table->enum('status', ['given', 'missed', 'refused', 'held'])->default('given');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inpatient_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('from_ward_id')->nullable()->constrained('wards')->nullOnDelete();
            $table->foreignId('from_bed_id')->nullable()->constrained('beds')->nullOnDelete();
            $table->foreignId('to_ward_id')->constrained('wards')->restrictOnDelete();
            $table->foreignId('to_bed_id')->constrained('beds')->restrictOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->timestamp('transferred_at');
            $table->timestamps();
        });

        Schema::create('discharge_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('prepared_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('final_diagnosis');
            $table->text('condition_on_discharge')->nullable();
            $table->text('procedures_done')->nullable();
            $table->text('hospital_course')->nullable();
            $table->text('investigations')->nullable();
            $table->text('treatment_given')->nullable();
            $table->text('discharge_medications')->nullable();
            $table->text('follow_up_instructions')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discharge_summaries');
        Schema::dropIfExists('inpatient_transfers');
        Schema::dropIfExists('medication_administrations');
        Schema::dropIfExists('medication_orders');
        Schema::dropIfExists('inpatient_vitals');
        Schema::dropIfExists('inpatient_notes');
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('wards');
    }
};
