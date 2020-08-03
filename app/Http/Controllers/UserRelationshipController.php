<?php

namespace App\Http\Controllers;

use App\User;
use App\UserRelationship;
use Illuminate\Http\Request;
use App\Http\Resources\UserRelationship as UserRelationshipResource;

class UserRelationshipController extends Controller
{
    public function store(User $user, Request $request)
    {
        $data = $request->validate([
            'data.attributes.related_user_id' => 'required|exists:users,id'
        ]);

    	$relationship = $user->relationships()->create([
    		'requested_id' => $data['data']['attributes']['related_user_id'],
    		'type' => 'pending'
    	]);

    	return new UserRelationshipResource($relationship);
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
