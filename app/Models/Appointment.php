<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Appointment extends Model
{
    use HasFactory;

    public const STATUSES = [
        'scheduled',
        'confirmed',
        'checked_in',
        'completed',
        'canceled',
        'no_show',
    ];

    protected $fillable = [
        'branch_id',
        'patient_id',
        'doctor_id',
        'service_type',
        'scheduled_at',
        'duration',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getEndTimeAttribute()
    {
        return $this->scheduled_at?->copy()->addMinutes($this->duration ?? 30);
    }

    public static function statusOptions(): array
    {
        return self::STATUSES;
    }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        if ($user->isSuperAdmin()) {
            return $branchId ? $query->where('branch_id', $branchId) : $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }
}
