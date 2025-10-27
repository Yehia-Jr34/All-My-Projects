<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfMeal extends Model
{
    public function diet_days ()
    {
        return $this->belongsToMany(DietDay::class);
    }

    use HasFactory;
}
