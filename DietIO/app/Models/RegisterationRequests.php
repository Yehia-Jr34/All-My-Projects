<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterationRequests extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phoneNumber',
        'certification_photo',
        'profile_photo',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function admin()
    {
        return $this->belongsToMany(Admin::class);
    }

}
