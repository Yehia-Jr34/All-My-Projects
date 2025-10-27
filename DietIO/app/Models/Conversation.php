<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsToMany(User::class);
    }

    public function doctor()
    {
        return $this->belongsToMany(Doctor::class);
    }

    protected $fillable = [
        'user_id',
        'doctor_id',
        'path'
    ];
}
