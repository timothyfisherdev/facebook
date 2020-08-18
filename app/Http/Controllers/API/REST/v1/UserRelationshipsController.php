<?php

namespace App\Http\Controllers\API\REST\v1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRelationship;
use App\Http\Requests\UpdateUserRelationship;

class UserRelationshipsController extends Controller
{
    public function store(StoreUserRelationship $request, User $requester)
    {
        $requester->requestRelationshipWith($request->validated()['user_id']);

        return response()->noContent(201);
    }

    public function update(UpdateUserRelationship $request, User $addressee, User $requester)
    {
        $addressee->updateRelationshipWith($requester, $request->validated()['action']);

        return response()->noContent();
    }
}
