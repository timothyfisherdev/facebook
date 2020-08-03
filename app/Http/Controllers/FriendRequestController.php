<?php

namespace App\Http\Controllers;

use App\FriendRequest;
use Illuminate\Http\Request;
use App\Http\Resources\FriendRequest as FriendRequestResource;

class FriendRequestController extends Controller
{
    public function store(Request $request)
    {
    	$data = $request->validate([
    		'data.attributes.user_id' => ''
    	]);

    	$friendRequest = auth()->user()->friendRequests()->create([
    		'requested_id' => $data['data']['attributes']['user_id']
    	]);

    	return new FriendRequestResource($friendRequest);
    }
}
