<?php

namespace App\Http\Controllers\API\REST\v1;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRelationship;
use App\Http\Requests\UpdateUserRelationship;

/**
 * Handles relationship requests between users.
 *
 * STORE (POST) - Request a relationship.
 * UPDATE (PUT) - Accept a relationship request.
 * DESTROY (DELETE) - Decline a relationship request.
 */
class UserRelationshipsController extends Controller
{
    /**
     * Creates a relationship request between two users.
     * 
     * @param  \App\Http\Requests\StoreUserRelationship    $request   Request.
     * @param  \App\User                                   $requester The user requesting the relationship.
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRelationship $request, User $requester) : Response
    {
        $requester->requestRelationshipWith($request->validated()['user_id']);

        return response()->noContent();
    }

    /**
     * Accepts a relationship request between two users via a PUT request.
     * 
     * @param  \App\Http\Requests\UpdateUserRelationship    $request   Request.
     * @param  \App\User                                    $addressee The recipient of the request.
     * @param  \App\User                                    $requester The user requesting the relationship.
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRelationship $request, User $addressee, User $requester) : Response
    {
        $addressee->acceptRelationshipRequestFrom($requester);

        return response()->noContent();
    }

    /**
     * Declines a relationship request between two users.
     * 
     * @param  \App\Http\Requests\UpdateUserRelationship     $request   Request.
     * @param  \App\User                                     $addressee The recipient of the request.
     * @param  \App\User                                     $requester The user requesting the relationship.
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(UpdateUserRelationship $request, User $addressee, User $requester) : Response
    {
        $addressee->declineRelationshipRequestFrom($requester);

        return response()->noContent();
    }
}
