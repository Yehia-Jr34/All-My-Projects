<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilePicture extends Model
{
    use HasFactory;

    public function doctor ()
    {
        return $this->belongsTo(Doctor::class);
    }

    protected $fillable  = [
        'doctor_id',
        'path'
    ];
}
