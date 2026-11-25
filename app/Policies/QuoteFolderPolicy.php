<?php

namespace App\Policies;

use App\Models\User;

class QuoteFolderPolicy
{
    public function manage(User $user): bool
    {
        return (bool) $user;
    }
}
