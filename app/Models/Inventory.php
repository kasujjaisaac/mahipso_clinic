<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\InventoryMovement;

class Inventory extends Model
{
    use HasFactory;

    public const STATUS_IN_STORE = 'in_store';
    public const STATUS_ASSIGNED = 'assigned';
    public const STATUS_DISPOSED = 'disposed';

    protected $fillable = [
        'item_name',
        'category',
        'sku',
        'quantity',
        'reorder_level',
        'unit',
        'unit_price',
        'supplier',
        'purchase_date',
        'expiry_date',
        'location',
        'status',
        'assigned_to',
        'assigned_at',
        'disposed_by',
        'disposed_at',
        'disposal_reason',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'assigned_at' => 'datetime',
        'disposed_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_IN_STORE => 'In Store',
            self::STATUS_ASSIGNED => 'Assigned to staff',
            self::STATUS_DISPOSED => 'Disposed',
        ];
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function disposedBy()
    {
        return $this->belongsTo(User::class, 'disposed_by');
    }

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class)->latest('performed_at');
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        return $query->where('item_name', 'like', "%{$term}%")
            ->orWhere('sku', 'like', "%{$term}%")
            ->orWhere('category', 'like', "%{$term}%")
            ->orWhere('supplier', 'like', "%{$term}%");
    }

    public function scopeStatus($query, ?string $status)
    {
        if (! $status) {
            return $query;
        }

        return $query->where('status', $status);
    }
}
