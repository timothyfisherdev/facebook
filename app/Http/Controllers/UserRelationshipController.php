<?php

namespace App\Http\Controllers;

use App\User;
use App\UserRelationship;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\Http\Resources\UserRelationship as UserRelationshipResource;

class UserRelationshipController extends Controller
{
    public function store(User $user, Request $request)
    {
        $this->authorize('store', [UserRelationship::class, $user]);

        $data = $request->validate([
            'data.*.id' => 'required|distinct|exists:users,id|not_in:' . $user->id
        ]);

        $user->createRelationships(Arr::pluck($data['data'], 'id'));

    	return response()->noContent();
    }

    public function update(User $user, UserRelationship $relationship, Request $request)
    {
        $this->authorize('update', [$relationship, $user]);

    	$data = $request->validate([
    		'data.attributes.type' => 'required'
    	]);

    	$relationship->update([
    		'type' => $data['data']['attributes']['type']
    	]);

    	return new UserRelationshipResource($relationship);
    }
}
