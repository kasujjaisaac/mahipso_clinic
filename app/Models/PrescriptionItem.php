<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrescriptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'prescription_order_id',
        'product_id',
        'quantity',
        'dosage',
        'frequency',
        'duration',
        'instructions',
        'unit_price',
        'total_price',
        'dispensed_quantity',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function prescriptionOrder()
    {
        return $this->belongsTo(PrescriptionOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
