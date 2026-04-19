<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VitalSigns extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id', 'weight', 'height', 'temperature', 'blood_pressure_systolic', 'blood_pressure_diastolic', 'pulse', 'respiratory_rate', 'notes'
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
