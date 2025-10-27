<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InvitationNotification extends Model
{
    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group(): BelongsTo {
        return $this->belongsTo(Group::class, 'group_id');
    }

    protected $fillable = [
        'user_id',
        'group_id',
        'notification_text',
        'status'
    ];
}
