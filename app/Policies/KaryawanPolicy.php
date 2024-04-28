<?php

namespace App\Policies;

use App\Models\Karyawan;
use App\Models\User;

class KaryawanPolicy
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
