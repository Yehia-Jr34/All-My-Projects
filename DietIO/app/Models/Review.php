<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $fillable =[
        'doctor_id',
        'user_id',
        'review',
        'week_number',
        'diet_id'
    ];
    

    use HasFactory;
}
