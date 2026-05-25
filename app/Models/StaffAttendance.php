<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffAttendance extends Model
{
    use HasFactory;

    protected $fillable = ['branch_id', 'user_id', 'work_date', 'clock_in', 'clock_out', 'status', 'notes'];

    protected $casts = ['work_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function branch() { return $this->belongsTo(Branch::class); }

    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isSuperAdmin() ? $query : $query->where('branch_id', $user->branch_id);
    }
}
