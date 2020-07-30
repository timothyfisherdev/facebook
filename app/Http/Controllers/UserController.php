<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
	public function me(Request $request)
	{
		return $this->show(auth()->id(), $request);
	}

    public function show(string $userId, Request $request)
    {
    	$user = QueryBuilder::for(User::class)
    		->allowedIncludes(['posts'])
    		->findOrFail($userId);

    	return new UserResource($user);
    }
}
