<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionItem extends Model
{
    use HasFactory;

    protected $fillable = ['requisition_id', 'item', 'unit_cost', 'quantity', 'frequency', 'total_cost'];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'quantity' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    public function requisition()
    {
        return $this->belongsTo(Requisition::class);
    }
}
