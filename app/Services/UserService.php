<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Check if a user is banned.
     *
     * @param  string|User  $userOrEmail  Email address or User model instance
     * @return bool
     */
    public static function isBanned(string|User $userOrEmail): bool
    {
        if ($userOrEmail instanceof User) {
            return $userOrEmail->banned == 1;
        }

        $user = User::where('email', $userOrEmail)->first();

        return $user && $user->banned == 1;
    }
}
