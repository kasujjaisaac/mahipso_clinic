<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HivRecord;
use Illuminate\Database\Eloquent\Builder;

class Visit extends Model
{
    use HasFactory;

    public const STAGE_CHECKED_IN = 'checked_in';
    public const STAGE_TRIAGE = 'triage';
    public const STAGE_CONSULTATION = 'consultation';
    public const STAGE_LABORATORY = 'laboratory';
    public const STAGE_PHARMACY = 'pharmacy';
    public const STAGE_BILLING = 'billing';
    public const STAGE_COMPLETED = 'completed';

    public const WORKFLOW_STAGES = [
        self::STAGE_CHECKED_IN => 'Checked in',
        self::STAGE_TRIAGE => 'Triage',
        self::STAGE_CONSULTATION => 'Consultation',
        self::STAGE_LABORATORY => 'Laboratory',
        self::STAGE_PHARMACY => 'Pharmacy',
        self::STAGE_BILLING => 'Billing',
        self::STAGE_COMPLETED => 'Completed',
    ];

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
        'workflow_stage',
        'checked_in_at',
        'triaged_at',
        'consultation_started_at',
        'lab_started_at',
        'pharmacy_started_at',
        'billing_started_at',
        'completed_at',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'checked_in_at' => 'datetime',
        'triaged_at' => 'datetime',
        'consultation_started_at' => 'datetime',
        'lab_started_at' => 'datetime',
        'pharmacy_started_at' => 'datetime',
        'billing_started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function provider() { return $this->belongsTo(User::class, 'provider_id'); }
    public function medicalRecords() { return $this->hasMany(MedicalRecord::class); }
    public function hivRecords() { return $this->hasMany(HivRecord::class); }

    public function vitalSigns() { return $this->hasOne(VitalSigns::class); }
    public function labTests() { return $this->hasMany(LabTest::class); }
    public function prescriptionOrders() { return $this->hasMany(PrescriptionOrder::class); }
    public function bills() { return $this->hasMany(Bill::class); }
    public function admission() { return $this->hasOne(Admission::class); }

    public function getWorkflowStageLabelAttribute(): string
    {
        return self::WORKFLOW_STAGES[$this->workflow_stage] ?? ucfirst(str_replace('_', ' ', (string) $this->workflow_stage));
    }

    public function moveToStage(string $stage): void
    {
        if (! array_key_exists($stage, self::WORKFLOW_STAGES)) {
            throw new \InvalidArgumentException('Unknown visit workflow stage.');
        }

        $timestampColumn = match ($stage) {
            self::STAGE_CHECKED_IN => 'checked_in_at',
            self::STAGE_TRIAGE => 'triaged_at',
            self::STAGE_CONSULTATION => 'consultation_started_at',
            self::STAGE_LABORATORY => 'lab_started_at',
            self::STAGE_PHARMACY => 'pharmacy_started_at',
            self::STAGE_BILLING => 'billing_started_at',
            self::STAGE_COMPLETED => 'completed_at',
        };

        $updates = [
            'workflow_stage' => $stage,
            $timestampColumn => $this->{$timestampColumn} ?? now(),
        ];

        if ($stage === self::STAGE_COMPLETED) {
            $updates['status'] = 'closed';
        } elseif ($this->status === 'closed') {
            $updates['status'] = 'open';
        }

        $this->update($updates);
    }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        if ($user->isSuperAdmin()) {
            return $branchId ? $query->where('branch_id', $branchId) : $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }
}
