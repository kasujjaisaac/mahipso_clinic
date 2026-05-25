<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'branch_admin', 'doctor', 'nurse', 'receptionist'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
    }

    public function test_staff_user_can_open_their_profile_edit_page(): void
    {
        $branch = Branch::create(['name' => 'Bukakata Clinic', 'code' => 'BUKAKATA', 'status' => 'active']);
        $user = User::factory()->create(['branch_id' => $branch->id]);
        $user->assignRole('nurse');

        $response = $this->actingAs($user)->get(route('profile.show'));

        $response->assertOk();
        $response->assertSee('Personal details');
        $response->assertSee('Change password');
        $response->assertSee($user->email);
        $response->assertSee('Nurse');
        $response->assertSee('Bukakata Clinic');
    }

    public function test_user_can_update_their_own_profile_details(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.test',
        ]);
        $user->assignRole('doctor');

        $response = $this
            ->withHeader('User-Agent', 'PHPUnit')
            ->actingAs($user)
            ->put(route('profile.update'), [
                'name' => 'New Name',
                'email' => 'new@example.test',
            ]);

        $response->assertRedirect(route('profile.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'email' => 'new@example.test',
        ]);
    }

    public function test_user_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);
        $user->assignRole('receptionist');

        $response = $this
            ->withHeader('User-Agent', 'PHPUnit')
            ->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'old-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response->assertRedirect(route('profile.show'));
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_legacy_profile_edit_route_returns_to_single_profile_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole('doctor');

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertRedirect(route('profile.show'));
    }
}
