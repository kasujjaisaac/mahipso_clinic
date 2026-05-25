<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'visit_id',
        'amount',
        'paid',
        'status',
        'payment_method',
        'insurance_claim_no',
        'billed_at',
        'due_at',
        'notes',
    ];

    protected $casts = [
        'billed_at' => 'datetime',
        'due_at' => 'datetime',
        'amount' => 'decimal:2',
        'paid' => 'decimal:2',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->whereHas('patient', fn ($patientQuery) => $patientQuery->where('branch_id', $user->branch_id));
    }

    // Accessor for balance
    public function getBalanceAttribute()
    {
        return $this->amount - $this->paid;
    }

    // Check if bill is overdue
    public function getIsOverdueAttribute()
    {
        return $this->due_at && $this->due_at->isPast() && $this->status !== 'paid';
    }
}
