<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model implements Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, CanResetPassword, \Illuminate\Auth\Authenticatable;

    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    public function feedback_replay()
    {
        return $this->hasMany(FeedbackReplay::class);
    }

    public function registeration_requests()
    {
        return $this->hasMany(RegisterationRequests::class);
    }

}
