<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\PayrollRun;
use App\Models\StaffAppraisal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HrModuleTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Employee $employee;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'branch_admin', 'guard_name' => 'web']);

        $branch = Branch::create(['name' => 'Masaka Main Clinic', 'code' => 'MASAKA', 'status' => 'active']);
        $department = Department::create(['name' => 'Clinical']);

        $this->admin = User::factory()->create(['branch_id' => $branch->id]);
        $this->admin->assignRole('branch_admin');

        $this->employee = Employee::create([
            'employee_no' => 'EMP-100',
            'first_name' => 'Grace',
            'last_name' => 'Nurse',
            'email' => 'grace@example.test',
            'phone' => '0700000000',
            'department_id' => $department->id,
            'branch_id' => $branch->id,
            'job_title' => 'Nurse',
            'status' => 'active',
            'hire_date' => now()->subYear()->toDateString(),
        ]);
    }

    public function test_hr_can_create_contract_appraisal_and_payroll_run(): void
    {
        $this->actingAs($this->admin)
            ->post(route('contracts.store'), [
                'employee_id' => $this->employee->id,
                'contract_no' => 'CON-100',
                'contract_type' => 'fixed_term',
                'job_title' => 'Nurse',
                'start_date' => now()->startOfMonth()->toDateString(),
                'end_date' => now()->addYear()->toDateString(),
                'salary_amount' => 1000000,
                'status' => 'active',
                'signed_at' => now()->toDateString(),
            ])
            ->assertRedirect(route('contracts.index'));

        $this->assertDatabaseHas('employee_contracts', ['contract_no' => 'CON-100', 'salary_amount' => 1000000]);

        $this->actingAs($this->admin)
            ->post(route('appraisals.store'), [
                'employee_id' => $this->employee->id,
                'period_start' => now()->startOfYear()->toDateString(),
                'period_end' => now()->endOfYear()->toDateString(),
                'score' => 87,
                'rating' => 'Good',
                'status' => 'completed',
                'reviewed_at' => now()->toDateString(),
            ])
            ->assertRedirect(route('appraisals.index'));

        $this->assertDatabaseHas('staff_appraisals', ['employee_id' => $this->employee->id, 'rating' => 'Good']);

        $this->actingAs($this->admin)
            ->post(route('payroll.store'), [
                'branch_id' => $this->employee->branch_id,
                'period_month' => now()->format('Y-m'),
            ])
            ->assertRedirect();

        $payroll = PayrollRun::with('items')->first();

        $this->assertNotNull($payroll);
        $this->assertEquals(1000000, (float) $payroll->net_total);
        $this->assertCount(1, $payroll->items);
    }

    public function test_hr_pages_render(): void
    {
        EmployeeContract::create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'contract_no' => 'CON-101',
            'contract_type' => 'permanent',
            'job_title' => 'Nurse',
            'start_date' => now()->toDateString(),
            'salary_amount' => 1200000,
            'status' => 'active',
        ]);

        StaffAppraisal::create([
            'employee_id' => $this->employee->id,
            'branch_id' => $this->employee->branch_id,
            'reviewer_id' => $this->admin->id,
            'period_start' => now()->startOfYear()->toDateString(),
            'period_end' => now()->endOfYear()->toDateString(),
            'rating' => 'Good',
            'status' => 'completed',
        ]);

        $this->actingAs($this->admin)->get(route('contracts.index'))->assertOk()->assertSee('CON-101');
        $this->actingAs($this->admin)->get(route('appraisals.index'))->assertOk()->assertSee('Good');
        $this->actingAs($this->admin)->get(route('payroll.index'))->assertOk()->assertSee('Payroll');
        $this->actingAs($this->admin)->get(route('employees.show', $this->employee))->assertOk()->assertSee('Contracts')->assertSee('Appraisals');
    }

    public function test_hr_can_manage_departments_for_employees(): void
    {
        $this->actingAs($this->admin)
            ->post(route('departments.store'), [
                'name' => 'Finance',
                'description' => 'Finance and administration staff',
            ])
            ->assertRedirect(route('departments.index'));

        $department = Department::where('name', 'Finance')->firstOrFail();
        $this->employee->update(['department_id' => $department->id]);

        $this->actingAs($this->admin)
            ->get(route('departments.show', $department))
            ->assertOk()
            ->assertSee('Finance')
            ->assertSee('EMP-100');

        $this->actingAs($this->admin)
            ->put(route('departments.update', $department), [
                'name' => 'Finance and Admin',
                'description' => 'Finance team',
            ])
            ->assertRedirect(route('departments.show', $department));

        $this->assertDatabaseHas('departments', ['name' => 'Finance and Admin']);

        $this->actingAs($this->admin)
            ->delete(route('departments.destroy', $department->fresh()))
            ->assertSessionHasErrors('department');
    }
}
