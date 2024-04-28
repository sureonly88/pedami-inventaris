<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Subdivisi;

class SubdivisiPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }
    public function viewAny(User $user): bool
    {
       return $user->role === "admin";
    }
}
