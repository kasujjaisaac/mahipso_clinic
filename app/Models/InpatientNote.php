<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InpatientNote extends Model
{
    use HasFactory;

    protected $fillable = ['admission_id', 'author_id', 'note_type', 'subjective', 'objective', 'assessment', 'plan', 'note', 'recorded_at'];

    protected function casts(): array
    {
        return ['recorded_at' => 'datetime'];
    }

    public function admission() { return $this->belongsTo(Admission::class); }
    public function author() { return $this->belongsTo(User::class, 'author_id'); }
}
