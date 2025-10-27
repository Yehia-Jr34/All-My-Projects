<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileReport extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function file(): BelongsTo {
        return $this->belongsTo(Files::class, 'file_id');
    }

    protected $fillable = [
        'user_id',
        'file_id',
        'operation'
    ];
}
