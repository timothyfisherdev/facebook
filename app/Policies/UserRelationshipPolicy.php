<?php

namespace App\Policies;

use App\User;
use App\UserRelationship;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserRelationshipPolicy
{
    use HandlesAuthorization;

    /**
     * Ensures that when an update request to a user relationship occurs
     * that the currently authenticated user is making the request
     * and that the recipient of the relationship request
     * is the only one that can make updates to it.
     * 
     * @param  UserRelationship $relationship  The relationship.
     * @param  User             $recipientUser The user passed from the API call.
     * 
     * @return boolean                        True if authorized, false otherwise.
     */
    public function update(User $authUser, UserRelationship $relationship, User $recipientUser)
    {
        return $authUser->is($recipientUser)
            && $relationship->requested_id === $recipientUser->id;
    }
}
