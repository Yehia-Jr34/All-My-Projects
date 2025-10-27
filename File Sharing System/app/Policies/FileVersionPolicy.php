<?php

namespace App\Policies;

use App\Models\FileVersion;
use App\Models\User;

class FileVersionPolicy
{
   public function ownFileVersion(User $user , FileVersion $fileVersion) : bool {

       return $user->id === $fileVersion->user_id;

   }
}
