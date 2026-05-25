<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrugInteraction extends Model
{
    use HasFactory;

    protected $fillable = ['drug_a', 'drug_b', 'severity', 'warning', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}
