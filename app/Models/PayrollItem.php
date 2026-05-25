<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'basic_pay',
        'allowances',
        'deductions',
        'net_pay',
        'notes',
    ];

    protected $casts = [
        'basic_pay' => 'decimal:2',
        'allowances' => 'decimal:2',
        'deductions' => 'decimal:2',
        'net_pay' => 'decimal:2',
    ];

    public function payrollRun() { return $this->belongsTo(PayrollRun::class); }
    public function employee() { return $this->belongsTo(Employee::class); }
}
