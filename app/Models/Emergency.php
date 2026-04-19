<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emergency extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'type',
        'description',
        'status',
        'reported_at',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
