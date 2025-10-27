<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Complaint extends Model
{
    protected $table = 'complaints';

    protected $fillable = [
        'complaint',
        'trainee_id',
        'status',
        'type'
    ];

    public function trainee():BelongsTo
    {
        return $this->belongsTo(Trainee::class);
    }

    public function complaint_answer(): HasOne
    {
        return $this->hasOne(ComplaintAnswer::class);
    }
}
