<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'period_month',
        'status',
        'gross_total',
        'deductions_total',
        'net_total',
        'prepared_by',
        'approved_by',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'period_month' => 'date',
        'gross_total' => 'decimal:2',
        'deductions_total' => 'decimal:2',
        'net_total' => 'decimal:2',
        'paid_at' => 'date',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function preparedBy() { return $this->belongsTo(User::class, 'prepared_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function items() { return $this->hasMany(PayrollItem::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isSuperAdmin() ? $query : $query->where('branch_id', $user->branch_id);
    }
}
