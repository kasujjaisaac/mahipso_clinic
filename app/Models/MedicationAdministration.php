<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicationAdministration extends Model
{
    use HasFactory;

    protected $fillable = ['medication_order_id', 'administered_by', 'scheduled_at', 'administered_at', 'status', 'notes'];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'administered_at' => 'datetime',
        ];
    }

    public function medicationOrder() { return $this->belongsTo(MedicationOrder::class); }
    public function administeredBy() { return $this->belongsTo(User::class, 'administered_by'); }
}
