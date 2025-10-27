<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GlobalArticleCategory extends Model
{
    protected $table = 'global_article_categories';

    protected $fillable = [
        'article_id',
        'category_id',
        'updated_at',
        'created_at',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(GlobalArticle::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
