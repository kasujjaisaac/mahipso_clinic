<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_no', 'first_name', 'last_name', 'other_names', 'email', 'phone', 'photo', 'department_id', 'branch_id', 'job_title', 'role_name', 'status', 'hire_date', 'termination_date'
    ];

    public function department() { return $this->belongsTo(Department::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function contracts() { return $this->hasMany(EmployeeContract::class); }
    public function appraisals() { return $this->hasMany(StaffAppraisal::class); }
    public function payrollItems() { return $this->hasMany(PayrollItem::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }
}
