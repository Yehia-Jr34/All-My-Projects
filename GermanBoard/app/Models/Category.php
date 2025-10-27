<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'name_du',
    ];

    public function article_categories(): HasMany
    {
        return $this->hasMany(GlobalArticleCategory::class);
    }

    public function training(): HasMany
    {
        return $this->hasMany(Training::class);
    }

    public function training_categories(): HasMany
    {
        return $this->hasMany(TrainingCategory::class);
    }
}
