<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BranchIsolationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_branch_staff_only_see_their_own_branch_card(): void
    {
        [$masaka, $bukakata] = $this->makeBranches();

        $nurse = User::factory()->create(['branch_id' => $bukakata->id]);
        $nurse->assignRole('nurse');

        $response = $this->actingAs($nurse)->get(route('branches.cards'));

        $response->assertOk();
        $response->assertSee('Bukakata Clinic');
        $response->assertDontSee('Masaka Main Clinic');
    }

    public function test_super_admin_can_see_all_branch_cards(): void
    {
        [$masaka, $bukakata, $kalangala] = $this->makeBranches();

        $superAdmin = User::factory()->create(['branch_id' => null]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('branches.cards'));

        $response->assertOk();
        $response->assertSee('Masaka Main Clinic');
        $response->assertSee('Bukakata Clinic');
        $response->assertSee('Kalangala Clinic');
    }

    public function test_branch_staff_only_see_patients_from_their_own_clinic(): void
    {
        [$masaka, $bukakata] = $this->makeBranches();

        Patient::create([
            'branch_id' => $masaka->id,
            'mrn' => 'MRN-010',
            'first_name' => 'Jane',
            'last_name' => 'Masaka',
            'status' => 'active',
        ]);

        Patient::create([
            'branch_id' => $bukakata->id,
            'mrn' => 'MRN-020',
            'first_name' => 'Brian',
            'last_name' => 'Bukakata',
            'status' => 'active',
        ]);

        $receptionist = User::factory()->create(['branch_id' => $bukakata->id]);
        $receptionist->assignRole('receptionist');

        $response = $this->actingAs($receptionist)->get(route('patients.index'));

        $response->assertOk();
        $response->assertSee('Brian Bukakata');
        $response->assertDontSee('Jane Masaka');
    }

    public function test_branch_user_cannot_access_patient_from_another_branch(): void
    {
        [$masaka, $bukakata] = $this->makeBranches();

        $foreignPatient = Patient::create([
            'branch_id' => $masaka->id,
            'mrn' => 'MRN-001',
            'first_name' => 'Jane',
            'last_name' => 'Masaka',
            'status' => 'active',
        ]);

        $doctor = User::factory()->create(['branch_id' => $bukakata->id]);
        $doctor->assignRole('doctor');

        $response = $this->actingAs($doctor)->get(route('patients.show', $foreignPatient));

        $response->assertNotFound();
    }

    public function test_super_admin_can_filter_appointments_by_branch(): void
    {
        [$masaka, $bukakata] = $this->makeBranches();

        $masakaPatient = Patient::create([
            'branch_id' => $masaka->id,
            'mrn' => 'MRN-101',
            'first_name' => 'Mary',
            'last_name' => 'Masaka',
            'status' => 'active',
        ]);

        $bukakataPatient = Patient::create([
            'branch_id' => $bukakata->id,
            'mrn' => 'MRN-202',
            'first_name' => 'Brian',
            'last_name' => 'Bukakata',
            'status' => 'active',
        ]);

        Appointment::create([
            'branch_id' => $masaka->id,
            'patient_id' => $masakaPatient->id,
            'scheduled_at' => now()->addDay(),
            'duration' => 30,
            'status' => 'scheduled',
        ]);

        Appointment::create([
            'branch_id' => $bukakata->id,
            'patient_id' => $bukakataPatient->id,
            'scheduled_at' => now()->addDays(2),
            'duration' => 30,
            'status' => 'scheduled',
        ]);

        $superAdmin = User::factory()->create(['branch_id' => null]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('appointments.index', ['branch' => $bukakata->id]));

        $response->assertOk();
        $response->assertSee('Brian Bukakata');
        $response->assertDontSee('Mary Masaka');
    }

    public function test_branch_user_cannot_create_appointment_for_patient_from_other_branch(): void
    {
        [$masaka, $bukakata] = $this->makeBranches();

        $foreignPatient = Patient::create([
            'branch_id' => $masaka->id,
            'mrn' => 'MRN-303',
            'first_name' => 'Alice',
            'last_name' => 'Foreign',
            'status' => 'active',
        ]);

        $receptionist = User::factory()->create(['branch_id' => $bukakata->id]);
        $receptionist->assignRole('receptionist');

        $response = $this->actingAs($receptionist)->post(route('appointments.store'), [
            'patient_id' => $foreignPatient->id,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
            'duration' => 30,
        ]);

        $response->assertNotFound();
        $this->assertDatabaseCount('appointments', 0);
    }

    public function test_branch_staff_cannot_access_the_admin_dashboard(): void
    {
        [, $bukakata] = $this->makeBranches();

        $nurse = User::factory()->create(['branch_id' => $bukakata->id]);
        $nurse->assignRole('nurse');

        $response = $this->actingAs($nurse)->get(route('admin.dashboard'));

        $response->assertForbidden();
    }

    private function makeBranches(): array
    {
        $masaka = Branch::create(['name' => 'Masaka Main Clinic', 'code' => 'MASAKA', 'status' => 'active']);
        $bukakata = Branch::create(['name' => 'Bukakata Clinic', 'code' => 'BUKAKATA', 'status' => 'active']);
        $kalangala = Branch::create(['name' => 'Kalangala Clinic', 'code' => 'KALANGALA', 'status' => 'active']);

        return [$masaka, $bukakata, $kalangala];
    }
}
