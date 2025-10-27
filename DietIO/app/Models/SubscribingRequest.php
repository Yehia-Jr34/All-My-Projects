<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribingRequest extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function payment_requests()
    {
        return $this->hasOne(SubscribingRequest::class);
    }

    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'message',
        'status'
    ];

    protected $hidden = [
        'updated_at',
        'created_at',
    ];
}
