<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'body',
        'type', // reminder, alert, info, etc.
        'read_at',
        'scheduled_for',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
