<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $hidden = [
        'activity_id',
        'type_of_diet_id',
        'updated_at',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tag()
    {
        return $this->hasOne(Tag::class);
    }

    public function activity()
    {
        return $this->hasOne(Activity::class);
    }

    public function type_of_diet()
    {
        return $this->hasOne(TypeOfDiet::class);
    }

    protected $fillable = [
        'user_id',
        'weight',
        'height',
        'age',
        'gender',
        'waistline',
        'buttocks_cir',
        'target',
        'number_of_meals',
        'activity_id',
        'type_of_diet_id',
        'diseases',
        'surgery',
        'wake_up',
        'sleep',
        'job',
        'daily_routine',
        'notes',
        'study'
    ];
    use HasFactory;
}
