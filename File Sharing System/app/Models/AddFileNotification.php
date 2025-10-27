<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddFileNotification extends Model
{
    public function file(): BelongsTo
    {
        return $this->belongsTo(Files::class, 'file_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'file_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'file_id');
    }

    protected $fillable = [
       'file_id',
       'group_id',
       'user_id',
       'status'
    ];
}
