<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = [
        'trainee_id',
        'training_id',
        'value',
    ];
}
