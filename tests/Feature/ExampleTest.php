<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_guest_root_redirects_to_login(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }

    public function test_super_admin_root_redirects_to_admin_dashboard(): void
    {
        $superAdmin = User::factory()->create(['branch_id' => null]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get('/');

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_branch_staff_root_redirects_to_clinic_cards(): void
    {
        $branch = Branch::create(['name' => 'Masaka Main Clinic', 'code' => 'MASAKA', 'status' => 'active']);

        $nurse = User::factory()->create(['branch_id' => $branch->id]);
        $nurse->assignRole('nurse');

        $response = $this->actingAs($nurse)->get('/');

        $response->assertRedirect(route('branches.cards'));
    }

    public function test_guest_is_redirected_to_login_from_protected_clinic_cards_route(): void
    {
        $response = $this->get(route('branches.cards'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_reach_patient_index(): void
    {
        $branch = Branch::create(['name' => 'Bukakata Clinic', 'code' => 'BUKAKATA', 'status' => 'active']);

        $user = User::factory()->create(['branch_id' => $branch->id]);
        $user->assignRole('nurse');

        $response = $this->actingAs($user)->get(route('patients.index'));

        $response->assertOk();
    }

    public function test_super_admin_login_redirects_to_admin_dashboard(): void
    {
        $superAdmin = User::factory()->create(['branch_id' => null]);
        $superAdmin->assignRole('super_admin');

        $response = $this->post(route('login'), [
            'email' => $superAdmin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_super_admin_can_view_admin_dashboard(): void
    {
        $superAdmin = User::factory()->create(['branch_id' => null]);
        $superAdmin->assignRole('super_admin');

        $response = $this->actingAs($superAdmin)->get(route('admin.dashboard'));

        $response->assertOk();
        $response->assertSee('Quick access');
        $response->assertSee('Manage branches');
    }

    public function test_branch_staff_login_redirects_to_clinic_cards(): void
    {
        $branch = Branch::create(['name' => 'Kalangala Clinic', 'code' => 'KALANGALA', 'status' => 'active']);

        $receptionist = User::factory()->create(['branch_id' => $branch->id]);
        $receptionist->assignRole('receptionist');

        $response = $this->post(route('login'), [
            'email' => $receptionist->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('branches.cards'));
    }
}
