<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HivRecord;
use Illuminate\Database\Eloquent\Builder;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'patient_id',
        'appointment_id',
        'provider_id',
        'visit_date',
        'visit_type',
        'chief_complaint',
        'notes',
        'status',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function medicalRecords() { return $this->hasMany(MedicalRecord::class); }
    public function hivRecords() { return $this->hasMany(HivRecord::class); }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        if ($user->isSuperAdmin()) {
            return $branchId ? $query->where('branch_id', $branchId) : $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }
}
