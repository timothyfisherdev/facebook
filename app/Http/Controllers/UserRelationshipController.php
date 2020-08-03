<?php

namespace App\Http\Controllers;

use App\User;
use App\UserRelationship;
use Illuminate\Http\Request;
use App\Http\Requests\StoreUserRelationship;
use App\Http\Resources\UserRelationship as UserRelationshipResource;

class UserRelationshipController extends Controller
{
    public function store(User $user, StoreUserRelationship $request)
    {
    	$relationship = $user->relationships()->create([
    		'requested_id' => $request->validated()['data']['attributes']['related_user_id'],
    		'type' => 'pending'
    	]);

    	return new UserRelationshipResource($relationship);
    }

    public function update(User $user, UserRelationship $relationship, Request $request)
    {
    	$data = $request->validate([
    		'data.attributes.type' => ''
    	]);

    	$relationship->update([
    		'type' => $data['data']['attributes']['type']
    	]);

    	return new UserRelationshipResource($relationship);
    }
}
