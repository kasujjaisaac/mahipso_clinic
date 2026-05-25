<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'requested_by',
        'line_supervisor_id',
        'serial_number',
        'department',
        'requested_at',
        'status',
        'total_amount',
        'amount_in_words',
        'purpose',
        'supervisor_comments',
        'supervisor_reviewed_at',
        'checked_by',
        'checked_at',
        'approved_by',
        'approved_at',
        'finance_comments',
    ];

    protected $casts = [
        'requested_at' => 'date',
        'total_amount' => 'decimal:2',
        'supervisor_reviewed_at' => 'datetime',
        'checked_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
    public function lineSupervisor() { return $this->belongsTo(User::class, 'line_supervisor_id'); }
    public function checkedBy() { return $this->belongsTo(User::class, 'checked_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function items() { return $this->hasMany(RequisitionItem::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where(function ($q) use ($user) {
            $q->where('requested_by', $user->id)
                ->orWhere('line_supervisor_id', $user->id);

            if ($user->hasRole(['branch_admin', 'finance_officer'])) {
                $q->orWhere('branch_id', $user->branch_id);
            }
        });
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->requested_by === $user->id && in_array($this->status, ['draft', 'changes_requested'], true);
    }
}
