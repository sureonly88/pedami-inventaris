<?php

namespace App\Policies;

use App\Models\User;
use App\Models\data_r2r4;

class datar2r4Policy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user): bool
    {
        return $user->role === "admin";
    }

    public function create(User $user): bool
    {
        return $user->role === "admin";
    }

    public function delete(User $user): bool
    {
        return $user->role === "admin";
    }

    public function deleteAny(User $user): bool
    {
        return $user->role === "admin";
    }
}
