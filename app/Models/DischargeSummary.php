<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DischargeSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'admission_id',
        'prepared_by',
        'final_diagnosis',
        'condition_on_discharge',
        'procedures_done',
        'hospital_course',
        'investigations',
        'treatment_given',
        'discharge_medications',
        'follow_up_instructions',
        'follow_up_date',
    ];

    protected function casts(): array
    {
        return ['follow_up_date' => 'date'];
    }

    public function admission() { return $this->belongsTo(Admission::class); }
    public function preparer() { return $this->belongsTo(User::class, 'prepared_by'); }
}
