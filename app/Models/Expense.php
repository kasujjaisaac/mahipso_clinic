<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'category',
        'subcategory',
        'description',
        'amount',
        'vendor',
        'invoice_number',
        'payment_method',
        'paid_at',
        'due_at',
        'status',
        'recurring',
        'frequency',
        'notes',
        'receipt_path',
    ];

    protected $casts = [
        'paid_at' => 'date',
        'due_at' => 'date',
        'recurring' => 'boolean',
        'amount' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
