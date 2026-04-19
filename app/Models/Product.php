<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_id', 'product_category_id', 'name', 'purchase_date', 'expiry_date', 'price', 'image_path', 'quantity', 'minimum_stock', 'status', 'added_by'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiry_date' => 'date',
        'expires_soon_notified_at' => 'datetime',
    ];

    public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(ProductAuditLog::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity < minimum_stock');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereBetween('expiry_date', [now()->toDateString(), now()->addDays($days)->toDateString()]);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now()->toDateString());
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) return $query;
        return $query->where('name', 'like', "%{$search}%");
    }

    public function scopeByCategory($query, $categoryId)
    {
        if (!$categoryId) return $query;
        return $query->where('product_category_id', $categoryId);
    }

    // Accessors
    public function getIsExpiredAttribute()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getIsLowStockAttribute()
    {
        return $this->quantity < $this->minimum_stock;
    }

    public function getExpiresInDaysAttribute()
    {
        if (!$this->expiry_date) return null;
        return now()->diffInDays($this->expiry_date, false);
    }
}
