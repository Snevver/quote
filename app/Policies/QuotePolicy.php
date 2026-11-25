<?php

namespace App\Policies;

use App\Models\User;

class QuotePolicy
{
    public function manage(User $user): bool
    {
        return (bool) $user;
    }
}
