<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bed extends Model
{
    use HasFactory;

    public const STATUS_AVAILABLE = 'available';
    public const STATUS_OCCUPIED = 'occupied';
    public const STATUS_RESERVED = 'reserved';
    public const STATUS_CLEANING = 'cleaning';
    public const STATUS_MAINTENANCE = 'maintenance';

    protected $fillable = [
        'ward_id',
        'bed_number',
        'status',
        'notes',
        'last_cleaned_at',
    ];

    protected function casts(): array
    {
        return [
            'last_cleaned_at' => 'datetime',
        ];
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function currentAdmission()
    {
        return $this->hasOne(Admission::class)->whereIn('status', ['admitted', 'ready_for_discharge', 'pending_clearance']);
    }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        return $query->whereHas('ward', fn ($ward) => $ward->visibleTo($user, $branchId));
    }
}
