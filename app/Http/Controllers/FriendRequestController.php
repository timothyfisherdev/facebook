<?php

namespace App\Http\Controllers;

use App\FriendRequest;
use Illuminate\Http\Request;
use App\Http\Requests\StoreFriendRequest;
use App\Http\Resources\FriendRequest as FriendRequestResource;

class FriendRequestController extends Controller
{
    public function store(StoreFriendRequest $request)
    {
    	$friendRequest = auth()->user()->friendRequests()->create([
    		'requested_id' => $request->validated()['data']['attributes']['user_id']
    	]);

    	return new FriendRequestResource($friendRequest);
    }
}
