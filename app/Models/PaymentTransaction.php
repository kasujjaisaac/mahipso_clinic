<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'bill_id',
        'patient_id',
        'received_by',
        'amount',
        'method',
        'reference',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function bill() { return $this->belongsTo(Bill::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        if ($user->isSuperAdmin()) {
            return $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }
}
