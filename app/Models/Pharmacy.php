<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pharmacy extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Analytics methods
    public function getTotalRevenueAttribute($from = null, $to = null)
    {
        $query = $this->sales()->where('status', 'completed');
        if ($from && $to) {
            $query->whereBetween('sale_date', [$from, $to]);
        }
        return $query->sum('total_price');
    }

    public function getLowStockProductsAttribute()
    {
        return $this->products()->lowStock()->count();
    }

    public function getExpiringProductsAttribute()
    {
        return $this->products()->expiringSoon()->count();
    }

    public function getExpiredProductsAttribute()
    {
        return $this->products()->expired()->count();
    }

    public function getInventoryValueAttribute()
    {
        return $this->products()
            ->active()
            ->get()
            ->sum(fn($p) => $p->quantity * $p->price);
    }
}
