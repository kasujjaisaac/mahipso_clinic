<?php

namespace Tests\Feature;

use App\Models\Admission;
use App\Models\Bed;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InpatientManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist', 'pharmacist'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_patient_can_be_admitted_and_bed_is_occupied(): void
    {
        [$branch, $doctor, $patient, $visit, $ward, $bed] = $this->makeAdmissionContext();
        $admin = User::factory()->create(['branch_id' => null]);
        $admin->assignRole('super_admin');

        $response = $this->actingAs($admin)->post(route('admissions.store'), [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'admitting_doctor_id' => $doctor->id,
            'current_doctor_id' => $doctor->id,
            'ward_id' => $ward->id,
            'bed_id' => $bed->id,
            'admission_type' => 'emergency',
            'admitted_at' => now()->toDateTimeString(),
            'reason_for_admission' => 'Severe dehydration requiring observation.',
            'provisional_diagnosis' => 'Dehydration',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('admissions', [
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'bed_id' => $bed->id,
            'status' => Admission::STATUS_ADMITTED,
        ]);
        $this->assertSame(Bed::STATUS_OCCUPIED, $bed->fresh()->status);
    }

    public function test_inpatient_chart_records_vitals_notes_and_discharge(): void
    {
        [$branch, $doctor, $patient, $visit, $ward, $bed] = $this->makeAdmissionContext();
        $admin = User::factory()->create(['branch_id' => null]);
        $admin->assignRole('super_admin');
        $admission = Admission::create([
            'branch_id' => $branch->id,
            'patient_id' => $patient->id,
            'visit_id' => $visit->id,
            'admitting_doctor_id' => $doctor->id,
            'current_doctor_id' => $doctor->id,
            'ward_id' => $ward->id,
            'bed_id' => $bed->id,
            'admission_no' => Admission::nextAdmissionNo($branch->id),
            'admission_type' => 'emergency',
            'status' => Admission::STATUS_ADMITTED,
            'admitted_at' => now(),
            'reason_for_admission' => 'Observation',
        ]);
        $bed->update(['status' => Bed::STATUS_OCCUPIED]);

        $this->actingAs($admin)->post(route('admissions.vitals.store', $admission), [
            'temperature' => 37.1,
            'pulse' => 90,
            'recorded_at' => now()->toDateTimeString(),
        ])->assertRedirect();

        $this->actingAs($admin)->post(route('admissions.notes.store', $admission), [
            'note_type' => 'doctor_round',
            'assessment' => 'Stable',
            'plan' => 'Continue fluids',
            'recorded_at' => now()->toDateTimeString(),
        ])->assertRedirect();

        foreach (['nursing_cleared', 'pharmacy_cleared', 'billing_cleared'] as $clearance) {
            $this->actingAs($admin)->post(route('admissions.clearance', $admission), [
                'clearance' => $clearance,
            ])->assertRedirect();
        }

        $this->actingAs($admin)->post(route('admissions.discharge', $admission), [
            'discharge_type' => 'improved',
            'final_diagnosis' => 'Recovered dehydration',
            'condition_on_discharge' => 'Stable',
        ])->assertRedirect();

        $this->assertDatabaseHas('inpatient_vitals', ['admission_id' => $admission->id, 'pulse' => 90]);
        $this->assertDatabaseHas('inpatient_notes', ['admission_id' => $admission->id, 'assessment' => 'Stable']);
        $this->assertDatabaseHas('discharge_summaries', ['admission_id' => $admission->id, 'final_diagnosis' => 'Recovered dehydration']);
        $this->assertSame(Admission::STATUS_DISCHARGED, $admission->fresh()->status);
        $this->assertSame(Bed::STATUS_CLEANING, $bed->fresh()->status);
        $this->assertSame(Visit::STAGE_COMPLETED, $visit->fresh()->workflow_stage);
    }

    private function makeAdmissionContext(): array
    {
        $branch = Branch::create(['name' => 'Masaka Main Clinic', 'code' => 'MASAKA', 'status' => 'active']);
        $doctor = User::factory()->create(['branch_id' => $branch->id]);
        $doctor->assignRole('doctor');
        $patient = Patient::create([
            'branch_id' => $branch->id,
            'mrn' => 'MRN-ADM-001',
            'first_name' => 'Jane',
            'last_name' => 'Inpatient',
            'status' => 'active',
        ]);
        $visit = Visit::create([
            'branch_id' => $branch->id,
            'patient_id' => $patient->id,
            'provider_id' => $doctor->id,
            'visit_date' => now(),
            'visit_type' => 'general',
            'status' => 'open',
            'workflow_stage' => Visit::STAGE_CONSULTATION,
            'checked_in_at' => now(),
        ]);
        $ward = Ward::create([
            'branch_id' => $branch->id,
            'name' => 'Medical Ward',
            'type' => 'medical',
            'gender_restriction' => 'none',
            'is_active' => true,
        ]);
        $bed = $ward->beds()->create(['bed_number' => '1', 'status' => Bed::STATUS_AVAILABLE]);

        return [$branch, $doctor, $patient, $visit, $ward, $bed];
    }
}
