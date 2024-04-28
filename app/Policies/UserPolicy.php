<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
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
        // if($user->role == "admin"){

        // }else{
        //     return false;
        // }
       return $user->role === "admin";
    }
}
