<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class Doctor extends Model implements Authenticatable
{

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function doctor_wallet()
    {
        return $this->hasOne(DoctorWallet::class);
    }

    public function certificationImage()
    {
        return $this->hasMany(CertificationsImages::class);
    }

    public function profilePicture()
    {
        return $this->hasOne(ProfilePicture::class);
    }

    public function conversation()
    {
        return $this->hasMany(Conversation::class);
    }

    public function registeration_requests()
    {
        return $this->hasOne(RegisterationRequests::class);
    }

    public function rate_doctors()
    {
        return $this->hasMany(rateDoctor::class);
    }

    public function fav_doctors()
    {
        return $this->hasMany(FavDoctor::class);
    }

    public function subscribing_requests()
    {
        return $this->hasMany(SubscribingRequest::class);
    }

    public function diet()
    {
        return $this->hasMany(Diet::class);
    }

    public function payment_requests()
    {
        return $this->hasMany(PaymentRequest::class);
    }

    use HasFactory, Searchable, HasApiTokens, \Illuminate\Auth\Authenticatable, CanResetPassword;

    public function toSearchableArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
        ];
    }

    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'password',
        'phoneNumber',
        'certification_photo',
        'profile_photo',
        'consultation_price',
        'rate',
        'number_of_rates'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'certification_photo',
        'profile_photo',
        'updated_at',
        'created_at',
    ];
}
