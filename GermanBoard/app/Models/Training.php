<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Training extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected
        $fillable = [
            'provider_id',
            'title_ar',
            'title_en',
            'title_du',
            'about_ar',
            'about_en',
            'about_du',
            'start_date',
            'end_date',
            'duration_in_hours',
            'price',
            'type',
            'language',
            'rate',
            'status',
            'training_site',
            'total_income'
        ];

    protected
        $hidden = [
            'media',
        ];

    public
    function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public
    function training_categories(): HasMany
    {
        return $this->hasMany(TrainingCategory::class);
    }

    public
    function training_attachments(): HasMany
    {
        return $this->hasMany(TrainingAttachment::class);
    }

    public
    function training_trainees(): HasMany
    {
        return $this->hasMany(TrainingTrainee::class);
    }

    public
    function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public
    function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public
    function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public
    function training_rates(): HasMany
    {
        return $this->hasMany(TrainingRate::class);
    }

    public
    function key_learning_objectives(): HasMany
    {
        return $this->hasMany(KeyLearningObjective::class);
    }

    public
    function training_tags(): HasMany
    {
        return $this->hasMany(TrainingTag::class);
    }

    public
    function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    public
    function internalTrainers()
    {
        return $this->belongsToMany(InternalTrainer::class, 'internal_trainer_trainings')
            ->using(InternalTrainerTraining::class)
            ->withTimestamps();
    }
}
