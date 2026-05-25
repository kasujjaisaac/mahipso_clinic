<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'visit_id',
        'patient_id',
        'provider_id',
        'status',
        'notes',
        'ordered_at',
        'dispensed_at',
        'dispensed_by',
    ];

    protected $casts = [
        'ordered_at' => 'datetime',
        'dispensed_at' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function visit() { return $this->belongsTo(Visit::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function dispensedBy() { return $this->belongsTo(User::class, 'dispensed_by'); }
    public function items() { return $this->hasMany(PrescriptionItem::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }

    public function getTotalAmountAttribute(): float
    {
        return (float) $this->items->sum('total_price');
    }
}
