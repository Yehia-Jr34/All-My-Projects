<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


//#[ObservedBy([UserObserver::class])]
class TrainingRate extends Model
{
    protected $fillable = [
        'trainee_id',
        'training_id',
        'value',
    ];


    // Relationship to training
    public function training()
    {
        return $this->belongsTo(Training::class);
    }
}
