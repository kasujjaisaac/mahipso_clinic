<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InpatientTransfer extends Model
{
    use HasFactory;

    protected $fillable = ['admission_id', 'from_ward_id', 'from_bed_id', 'to_ward_id', 'to_bed_id', 'requested_by', 'reason', 'notes', 'transferred_at'];

    protected function casts(): array
    {
        return ['transferred_at' => 'datetime'];
    }

    public function admission() { return $this->belongsTo(Admission::class); }
    public function fromWard() { return $this->belongsTo(Ward::class, 'from_ward_id'); }
    public function fromBed() { return $this->belongsTo(Bed::class, 'from_bed_id'); }
    public function toWard() { return $this->belongsTo(Ward::class, 'to_ward_id'); }
    public function toBed() { return $this->belongsTo(Bed::class, 'to_bed_id'); }
    public function requester() { return $this->belongsTo(User::class, 'requested_by'); }
}
