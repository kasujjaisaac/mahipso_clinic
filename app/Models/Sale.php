<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id', 'product_id', 'patient_id', 'visit_id', 'provider_id', 'quantity', 'total_price', 'sold_by', 'sale_date', 'status', 'void_reason', 'voided_by', 'voided_at', 'prescription_note'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
        'voided_at' => 'datetime',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function soldBy()
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public function voidedBy()
    {
        return $this->belongsTo(User::class, 'voided_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeVoided($query)
    {
        return $query->where('status', 'voided');
    }

    public function scopeRefunded($query)
    {
        return $query->where('status', 'refunded');
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'voided');
    }

    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('sale_date', [$from, $to]);
    }

    public function scopeByPharmacy($query, $pharmacyId)
    {
        return $query->where('pharmacy_id', $pharmacyId);
    }

    // Methods
    public function void($reason, $voidedBy)
    {
        if ($this->status === 'voided') {
            return false;
        }

        $this->update([
            'status' => 'voided',
            'void_reason' => $reason,
            'voided_by' => $voidedBy,
            'voided_at' => now(),
        ]);

        // Reverse inventory
        $this->product->increment('quantity', $this->quantity);
        ProductAuditLog::create([
            'product_id' => $this->product_id,
            'user_id' => $voidedBy,
            'action' => 'quantity_adjusted',
            'old_values' => ['quantity' => $this->product->quantity - $this->quantity],
            'new_values' => ['quantity' => $this->product->quantity],
            'reason' => "Sale void - {$reason}",
        ]);

        return true;
    }
}
