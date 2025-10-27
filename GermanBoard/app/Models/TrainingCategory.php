<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TrainingCategory extends Model
{
    protected $fillable = [
        'training_id',
        'category_id',
    ];
    public function trainings(): BelongsToMany
    {
        return $this->belongsToMany(Training::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
