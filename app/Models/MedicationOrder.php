<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationOrder extends Model
{
    use HasFactory;

    protected $fillable = ['admission_id', 'prescribed_by', 'medicine_name', 'dose', 'route', 'frequency', 'start_at', 'stop_at', 'status', 'instructions'];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'stop_at' => 'datetime',
        ];
    }

    public function admission() { return $this->belongsTo(Admission::class); }
    public function prescriber() { return $this->belongsTo(User::class, 'prescribed_by'); }
    public function administrations() { return $this->hasMany(MedicationAdministration::class); }
}
