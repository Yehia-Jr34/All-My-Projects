<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorWallet extends Model
{
    public function doctor ()
    {
        return $this->belongsTo(Doctor::class);
    }

    protected $fillable = [
        'doctor_id',
        'value'
    ];

    use HasFactory;

}
