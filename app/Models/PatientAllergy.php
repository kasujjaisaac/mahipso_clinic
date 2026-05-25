<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientAllergy extends Model
{
    use HasFactory;

    protected $fillable = ['patient_id', 'substance', 'reaction', 'severity', 'recorded_by'];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
