<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOfDiet extends Model
{
    public function file()
    {
        return $this->belongsToMany(Files::class);
    }

    public function diet()
    {
        return $this->belongsToMany(Diet::class);
    }

    use HasFactory;
}
