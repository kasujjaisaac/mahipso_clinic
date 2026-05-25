<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'supplier_id', 'requested_by', 'status', 'total_amount', 'notes', 'expected_at'];

    protected $casts = ['expected_at' => 'date', 'total_amount' => 'decimal:2'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function requestedBy() { return $this->belongsTo(User::class, 'requested_by'); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isSuperAdmin() ? $query : $query->where('branch_id', $user->branch_id);
    }
}
