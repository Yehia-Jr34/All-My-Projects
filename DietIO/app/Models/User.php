<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, SoftDeletes,  Searchable, HasFactory, Notifiable, CanResetPassword;

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function user_wallets()
    {
        return $this->hasOne(UserWallet::class);
    }

    public function userImages()
    {
        return $this->hasMany(UserImage::class);
    }

    public function rate_doctors()
    {
        return $this->hasMany(rateDoctor::class);
    }

    public function conversation()
    {
        return $this->hasMany(Conversation::class);
    }

    public function user_diets()
    {
        return $this->hasMany(UserDiet::class);
    }

    public function fav_doctor()
    {
        return $this->hasMany(FavDoctor::class);
    }

    public function subscribing_requests()
    {
        return $this->hasMany(SubscribingRequest::class);
    }

    public function payment_requests()
    {
        return $this->hasMany(PaymentRequest::class);
    }

    public function file()
    {
        return $this->hasOne(Files::class);
    }

    public function review()
    {
        return $this->hasMany(Review::class);
    }

    public function feedback()
    {
        return $this->hasMany(Feedback::class);
    }

    public function feedback_replay()
    {
        return $this->hasMany(FeedbackReplay::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phoneNumber',
        'google_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'updated_at',
        'created_at',
        'google_id',
        'deleted_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
