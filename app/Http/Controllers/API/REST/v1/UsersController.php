<?php

namespace App\Http\Controllers\API\REST\v1;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\REST\v1\User as UserResource;

class UsersController extends Controller
{
	public function me(Request $request)
	{
		return $this->show(auth()->user(), $request);
	}

    public function show(User $user, Request $request)
    {
    	return new UserResource($user);
    }
}
