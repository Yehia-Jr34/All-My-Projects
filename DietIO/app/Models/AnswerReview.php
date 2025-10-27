<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnswerReview extends Model
{
    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    protected $fillable = [
        'review_id',
        'answer',
        'image_path'
    ];

    use HasFactory;
}
