<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyTimesheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'user_id',
        'line_supervisor_id',
        'employee_number',
        'job_title',
        'month',
        'prepared_at',
        'status',
        'total_hours',
        'staff_comments',
        'supervisor_comments',
        'submitted_at',
        'supervisor_reviewed_at',
        'hr_received_by',
        'hr_received_at',
        'hr_comments',
    ];

    protected $casts = [
        'month' => 'date',
        'prepared_at' => 'date',
        'total_hours' => 'decimal:2',
        'submitted_at' => 'datetime',
        'supervisor_reviewed_at' => 'datetime',
        'hr_received_at' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function lineSupervisor() { return $this->belongsTo(User::class, 'line_supervisor_id'); }
    public function hrReceivedBy() { return $this->belongsTo(User::class, 'hr_received_by'); }
    public function entries() { return $this->hasMany(MonthlyTimesheetEntry::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
                ->orWhere('line_supervisor_id', $user->id);

            if ($user->hasRole(['branch_admin', 'hr_manager'])) {
                $q->orWhere('branch_id', $user->branch_id);
            }
        });
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id && in_array($this->status, ['draft', 'changes_requested'], true);
    }
}
