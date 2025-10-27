<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Group extends Model
{
    protected $guarded = ['id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_groups')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function owner():BelongsTo {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function files () : HasMany {

        return $this->hasMany(Files::class ,'group_id');

    }
}

