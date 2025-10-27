<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    protected $fillable = [
        'trainee_phone_number',
        'code',
        'expired_at',
    ];
}
