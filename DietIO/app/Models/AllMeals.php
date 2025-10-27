<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllMeals extends Model
{
    protected $fillable = [
        'name',
        'description',
        'meal_time',
    ];
    public function meals()
    {
        return $this->belongsToMany(Meal::class);
    }

    use HasFactory;
}
