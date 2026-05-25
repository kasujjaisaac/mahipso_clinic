<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InpatientVital extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'recorded_by',
        'temperature',
        'blood_pressure_systolic',
        'blood_pressure_diastolic',
        'pulse',
        'respiratory_rate',
        'oxygen_saturation',
        'weight',
        'intake_ml',
        'output_ml',
        'pain_score',
        'notes',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return ['recorded_at' => 'datetime'];
    }

    public function admission() { return $this->belongsTo(Admission::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }
}
