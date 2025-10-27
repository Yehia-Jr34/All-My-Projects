<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class InternalTrainer extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'phone_number',
        'provider_id',
        'gender'
    ];

    public function providers()
    {
        return $this->belongsToMany(Provider::class, 'provider_internal_trainers')
            ->using(ProviderInternalTrainer::class)
            ->withTimestamps();
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'internal_trainer_trainings')
            ->using(InternalTrainerTraining::class)
            ->withTimestamps();
    }

    public function actions():HasMany{
        return $this->hasMany(InternalTrainerAction::class);
    }
}
