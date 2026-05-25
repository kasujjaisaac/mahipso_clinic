<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class LabTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'visit_id',
        'ordered_by',
        'resulted_by',
        'test_type',
        'price',
        'status',
        'results',
        'result_flag',
        'is_billable',
        'ordered_at',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'ordered_at' => 'date',
        'completed_at' => 'date',
        'price' => 'decimal:2',
        'is_billable' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function orderedBy()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function resultedBy()
    {
        return $this->belongsTo(User::class, 'resulted_by');
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('branch_id', $user->branch_id));
    }
}
