<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class HivRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'patient_id',
        'provider_id',
        'test_type',
        'test_result',
        'cd4_count',
        'viral_load',
        'art_status',
        'regimen',
        'adherence',
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
