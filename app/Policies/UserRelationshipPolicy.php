<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRelationshipPolicy
{
    use HandlesAuthorization;

    /**
     * Ensure that the currently authenticated user in our application
     * is the user from the route. This ensures a user cannot create
     * relationships for other users on their behalf.
     * 
     * @param  User   $authUser  Current auth user passed by Laravel.
     * @param  User   $routeUser User passed via the route URL.
     * 
     * @return boolean           True if the same user, false otherwise.
     */
    public function store(User $authUser, User $routeUser)
    {
        return $routeUser->is($authUser);
    }
}
