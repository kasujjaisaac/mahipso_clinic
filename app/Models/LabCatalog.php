<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabCatalog extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'test_name', 'sample_type', 'unit', 'reference_range', 'price', 'is_active'];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where(fn ($q) => $q->whereNull('branch_id')->orWhere('branch_id', $user->branch_id));
    }
}
