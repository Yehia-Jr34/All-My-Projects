<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Files extends Model
{
    protected $guarded = ['id'];

    public function group () : BelongsTo {

        return $this->belongsTo(Group::class, 'group_id');

    }

    public function versions () : HasMany {

        return $this->hasMany(FileVersion::class ,'file_id');

    }
}
