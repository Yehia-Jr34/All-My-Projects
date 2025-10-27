<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietDay extends Model
{
    protected $fillable = [
        'day_number',
        'week_number',
        'diet_id',
        'meal_type_id',
        'meal_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'status',
        'meal_id',
        'diet_id',
        'meal_type_id',
        'day_number',
        'week_number',
        'id'
    ];

    public function meal ()
    {
        return $this->hasOne(Meal::class);
    }

    public function diet()
    {
        return $this->belongsTo(Diet::class);
    }

    public function type_of_meals ()
    {
        return $this->hasOne(TypeOfMeal::class);
    }

    use HasFactory;
}
