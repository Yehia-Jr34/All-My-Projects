<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    public function all_meals ()
    {
        return $this->hasOne(AllMeals::class);
    }

    public function diet_days ()
    {
        return $this->belongsToMany(DietDay::class);
    }

    use HasFactory;
}
