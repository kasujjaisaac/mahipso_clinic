<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    use HasFactory;

    public const STATUS_ADMITTED = 'admitted';
    public const STATUS_READY = 'ready_for_discharge';
    public const STATUS_PENDING_CLEARANCE = 'pending_clearance';
    public const STATUS_DISCHARGED = 'discharged';
    public const STATUS_TRANSFERRED = 'transferred';
    public const STATUS_DECEASED = 'deceased';
    public const STATUS_ABSCONDED = 'absconded';

    protected $fillable = [
        'branch_id',
        'patient_id',
        'visit_id',
        'admitting_doctor_id',
        'current_doctor_id',
        'ward_id',
        'bed_id',
        'admission_no',
        'admission_type',
        'status',
        'admitted_at',
        'expected_discharge_at',
        'discharge_started_at',
        'discharged_at',
        'discharge_type',
        'reason_for_admission',
        'provisional_diagnosis',
        'current_diagnosis',
        'care_plan',
        'payment_type',
        'next_of_kin_name',
        'next_of_kin_phone',
        'consent_notes',
        'nursing_cleared',
        'pharmacy_cleared',
        'billing_cleared',
    ];

    protected function casts(): array
    {
        return [
            'admitted_at' => 'datetime',
            'expected_discharge_at' => 'datetime',
            'discharge_started_at' => 'datetime',
            'discharged_at' => 'datetime',
            'nursing_cleared' => 'boolean',
            'pharmacy_cleared' => 'boolean',
            'billing_cleared' => 'boolean',
        ];
    }

    public function patient() { return $this->belongsTo(Patient::class); }
    public function visit() { return $this->belongsTo(Visit::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function ward() { return $this->belongsTo(Ward::class); }
    public function bed() { return $this->belongsTo(Bed::class); }
    public function admittingDoctor() { return $this->belongsTo(User::class, 'admitting_doctor_id'); }
    public function currentDoctor() { return $this->belongsTo(User::class, 'current_doctor_id'); }
    public function notes() { return $this->hasMany(InpatientNote::class); }
    public function vitals() { return $this->hasMany(InpatientVital::class); }
    public function medicationOrders() { return $this->hasMany(MedicationOrder::class); }
    public function transfers() { return $this->hasMany(InpatientTransfer::class); }
    public function dischargeSummary() { return $this->hasOne(DischargeSummary::class); }

    public function getStatusLabelAttribute(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getLengthOfStayAttribute(): int
    {
        return $this->admitted_at?->diffInDays($this->discharged_at ?? now()) ?? 0;
    }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        if ($user->isSuperAdmin()) {
            return $branchId ? $query->where('branch_id', $branchId) : $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }

    public static function nextAdmissionNo(int $branchId): string
    {
        $year = now()->format('Y');
        $last = static::where('branch_id', $branchId)
            ->where('admission_no', 'like', "ADM-{$year}-%")
            ->orderByDesc('id')
            ->first();

        $next = $last ? ((int) substr($last->admission_no, -5)) + 1 : 1;

        return sprintf('ADM-%s-%05d', $year, $next);
    }
}
