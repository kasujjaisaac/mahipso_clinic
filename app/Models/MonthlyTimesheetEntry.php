<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyTimesheetEntry extends Model
{
    use HasFactory;

    protected $fillable = ['monthly_timesheet_id', 'day', 'work_specification', 'time_start', 'time_finish', 'hours'];

    protected $casts = [
        'hours' => 'decimal:2',
    ];

    public function monthlyTimesheet()
    {
        return $this->belongsTo(MonthlyTimesheet::class);
    }
}
