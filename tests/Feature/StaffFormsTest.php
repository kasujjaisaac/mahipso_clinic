<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Requisition;
use App\Models\MonthlyTimesheet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StaffFormsTest extends TestCase
{
    use RefreshDatabase;

    protected User $supervisor;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (['super_admin', 'branch_admin', 'line_supervisor', 'finance_officer', 'hr_manager', 'doctor', 'nurse', 'receptionist'] as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $branch = Branch::create(['name' => 'Masaka Main Clinic', 'code' => 'MASAKA', 'status' => 'active']);

        $this->supervisor = User::factory()->create(['branch_id' => $branch->id]);
        $this->supervisor->assignRole('line_supervisor');

        $this->staff = User::factory()->create([
            'branch_id' => $branch->id,
            'department' => 'Finance',
            'job_title' => 'Officer',
            'employee_number' => 'EMP-001',
            'line_supervisor_id' => $this->supervisor->id,
        ]);
        $this->staff->assignRole('nurse');
    }

    public function test_staff_can_submit_requisition_to_line_supervisor(): void
    {
        $response = $this
            ->withHeader('User-Agent', 'PHPUnit')
            ->actingAs($this->staff)
            ->post(route('requisitions.store'), [
                'department' => 'Finance',
                'requested_at' => now()->format('Y-m-d'),
                'purpose' => 'Clinic supplies',
                'amount_in_words' => 'Twenty thousand shillings',
                'action' => 'submit',
                'items' => [
                    ['item' => 'Gloves', 'unit_cost' => 10000, 'quantity' => 2, 'frequency' => 'Once'],
                ],
            ]);

        $requisition = Requisition::first();

        $response->assertRedirect(route('requisitions.show', $requisition));
        $this->assertSame('submitted', $requisition->status);
        $this->assertSame($this->supervisor->id, $requisition->line_supervisor_id);
        $this->assertEquals(20000, (float) $requisition->total_amount);
    }

    public function test_line_supervisor_can_approve_submitted_requisition(): void
    {
        $requisition = Requisition::create([
            'branch_id' => $this->staff->branch_id,
            'requested_by' => $this->staff->id,
            'line_supervisor_id' => $this->supervisor->id,
            'serial_number' => 'REQ-TEST',
            'department' => 'Finance',
            'requested_at' => now(),
            'status' => 'submitted',
        ]);

        $response = $this
            ->withHeader('User-Agent', 'PHPUnit')
            ->actingAs($this->supervisor)
            ->post(route('requisitions.supervisor-review', $requisition), [
                'decision' => 'supervisor_approved',
                'supervisor_comments' => 'Approved',
            ]);

        $response->assertRedirect();
        $this->assertSame('supervisor_approved', $requisition->fresh()->status);
    }

    public function test_staff_can_submit_monthly_timesheet(): void
    {
        $entries = collect(range(1, 31))->map(fn ($day) => [
            'day' => $day,
            'work_specification' => $day === 1 ? 'Clinic support' : null,
            'time_start' => $day === 1 ? '08:00' : null,
            'time_finish' => $day === 1 ? '16:00' : null,
        ])->toArray();

        $response = $this
            ->withHeader('User-Agent', 'PHPUnit')
            ->actingAs($this->staff)
            ->post(route('timesheets.store'), [
                'employee_number' => 'EMP-001',
                'job_title' => 'Officer',
                'month' => now()->format('Y-m'),
                'prepared_at' => now()->format('Y-m-d'),
                'action' => 'submit',
                'entries' => $entries,
            ]);

        $timesheet = MonthlyTimesheet::first();

        $response->assertRedirect(route('timesheets.show', $timesheet));
        $this->assertSame('submitted', $timesheet->status);
        $this->assertSame($this->supervisor->id, $timesheet->line_supervisor_id);
        $this->assertEquals(8, (float) $timesheet->total_hours);
    }

    public function test_staff_can_view_my_requisitions_and_print_requisition(): void
    {
        $requisition = Requisition::create([
            'branch_id' => $this->staff->branch_id,
            'requested_by' => $this->staff->id,
            'line_supervisor_id' => $this->supervisor->id,
            'serial_number' => 'REQ-PRINT',
            'department' => 'Finance',
            'requested_at' => now(),
            'status' => 'approved',
            'total_amount' => 15000,
        ]);
        $requisition->items()->create([
            'item' => 'Stationery',
            'unit_cost' => 15000,
            'quantity' => 1,
            'total_cost' => 15000,
        ]);

        $this->actingAs($this->staff)->get(route('requisitions.mine'))
            ->assertOk()
            ->assertSee('My Requisitions')
            ->assertSee('Approved');

        $this->actingAs($this->staff)->get(route('requisitions.print', $requisition))
            ->assertOk()
            ->assertSee('REQUISITION')
            ->assertSee('REQ-PRINT');
    }

    public function test_staff_can_view_my_timesheets_and_print_timesheet(): void
    {
        $timesheet = MonthlyTimesheet::create([
            'branch_id' => $this->staff->branch_id,
            'user_id' => $this->staff->id,
            'line_supervisor_id' => $this->supervisor->id,
            'employee_number' => 'EMP-001',
            'job_title' => 'Officer',
            'month' => now()->startOfMonth(),
            'status' => 'hr_received',
            'total_hours' => 8,
        ]);
        $timesheet->entries()->create([
            'day' => 1,
            'work_specification' => 'Clinic support',
            'time_start' => '08:00',
            'time_finish' => '16:00',
            'hours' => 8,
        ]);

        $this->actingAs($this->staff)->get(route('timesheets.mine'))
            ->assertOk()
            ->assertSee('My Timesheets')
            ->assertSee('Received by HR');

        $this->actingAs($this->staff)->get(route('timesheets.print', $timesheet))
            ->assertOk()
            ->assertSee('MONTHLY TIME SHEET')
            ->assertSee('Clinic support');
    }
}
