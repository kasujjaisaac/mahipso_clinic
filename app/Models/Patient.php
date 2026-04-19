<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Visit;
use App\Models\MedicalRecord;
use App\Models\LabTest;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'mrn',
        'first_name',
        'last_name',
        'dob',
        'gender',
        'phone',
        'email',
        'address',
        'national_id',
        'insurance_provider',
        'insurance_number',
        'status',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }

    public function labTests()
    {
        return $this->hasMany(LabTest::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($patient) {
            if (empty($patient->mrn)) {
                $patient->mrn = $patient->generateMRN();
            }
        });
    }

    public function generateMRN()
    {
        // Get branch code (first 3 characters, uppercase)
        $branchCode = strtoupper(substr($this->branch->code ?? 'MAIN', 0, 3));
        
        // Get current year
        $year = date('Y');
        
        // Generate sequential number for this branch and year
        $lastPatient = self::where('branch_id', $this->branch_id)
            ->where('mrn', 'like', $branchCode . '-' . $year . '-%')
            ->orderBy('id', 'desc')
            ->first();
            
        if ($lastPatient) {
            // Extract the number from the last MRN
            $parts = explode('-', $lastPatient->mrn);
            $lastNumber = (int) end($parts);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format: BRANCH-YEAR-XXXXX (e.g., KLA-2026-00001)
        return sprintf('%s-%s-%05d', $branchCode, $year, $nextNumber);
    }

    public function scopeVisibleTo(Builder $query, User $user, ?int $branchId = null): Builder
    {
        if ($user->isSuperAdmin()) {
            return $branchId ? $query->where('branch_id', $branchId) : $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }
}
