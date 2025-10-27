<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplaintAnswer extends Model
{
    protected $table = 'complaint_answers';

    protected $fillable = [
        'complaint_id',
        'answer',
        'user_id'
    ];

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }
}
