<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FileVersion extends Model
{
    protected $guarded = ['id'];

    public function file () : BelongsTo {

        return $this->belongsTo(Files::class, 'file_id');

    }

    public function user () : BelongsTo {

        return $this->belongsTo(User::class, 'user_id');

    }
}
