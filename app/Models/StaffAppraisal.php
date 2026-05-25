<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAppraisal extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'branch_id',
        'reviewer_id',
        'period_start',
        'period_end',
        'score',
        'rating',
        'strengths',
        'improvement_areas',
        'goals',
        'status',
        'reviewed_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'score' => 'decimal:2',
        'reviewed_at' => 'date',
    ];

    public function employee() { return $this->belongsTo(Employee::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewer_id'); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isSuperAdmin() ? $query : $query->where('branch_id', $user->branch_id);
    }
}
