<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'branch_id',
        'contract_no',
        'contract_type',
        'job_title',
        'start_date',
        'end_date',
        'salary_amount',
        'status',
        'terms',
        'signed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'salary_amount' => 'decimal:2',
        'signed_at' => 'date',
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function branch() { return $this->belongsTo(Branch::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isSuperAdmin() ? $query : $query->where('branch_id', $user->branch_id);
    }
}
