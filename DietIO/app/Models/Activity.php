<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    public function file()
    {
        return $this->belongsToMany(Files::class);
    }
    use HasFactory;
}
