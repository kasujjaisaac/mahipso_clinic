<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'name',
        'code',
        'type',
        'gender_restriction',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function beds()
    {
        return $this->hasMany(Bed::class);
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        if ($user->isSuperAdmin()) {
            return $branchId ? $query->where('branch_id', $branchId) : $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }
}
