<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'provider_id',
        'symptoms',
        'diagnosis',
        'treatment',
        'plan',
        'notes',
    ];

    public function visit() { return $this->belongsTo(Visit::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        if ($user->isSuperAdmin()) {
            return $branchId
                ? $query->whereHas('visit', fn ($visitQuery) => $visitQuery->where('branch_id', $branchId))
                : $query;
        }

        return $query->whereHas('visit', fn ($visitQuery) => $visitQuery->where('branch_id', $user->branch_id));
    }
}
