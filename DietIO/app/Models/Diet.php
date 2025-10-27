<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diet extends Model
{

    protected $hidden = [
        'created_at',
        'updated_at',
        'status'

    ];

    protected $fillable = [
        'name',
        'duration',
        'price',
        'tag_id',
        'type_id',
        'review_frequency',
        'user_id',
        'doctor_id',
        
        

    ];

    public function type_of_diet()
    {
        return $this->hasOne(TypeOfDiet::class);
    }

    public function user_diets()
    {
        return $this->hasMany(UserDiet::class);
    }

    public function diet_days ()
    {
        return $this->hasMany(DietDay::class);
    }

    public function tag()
    {
        return $this->hasOne(Tag::class);
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    use HasFactory;
}
