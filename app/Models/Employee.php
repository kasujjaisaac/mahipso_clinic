<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_no', 'first_name', 'last_name', 'other_names', 'email', 'phone', 'photo', 'department_id', 'branch_id', 'job_title', 'status', 'hire_date', 'termination_date'
    ];

    public function department() { return $this->belongsTo(Department::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
