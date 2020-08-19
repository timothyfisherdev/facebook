<?php

namespace App\Http\Controllers\API\REST\v1;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\API\REST\v1\User as UserResource;

/**
 * Handles users.
 *
 * Limited functionality since the users resource is read-only
 * in our API.
 *
 * Most functionality is handled by the web controllers
 * from the Auth scaffolding.
 */
class UsersController extends Controller
{
	/**
	 * Method for getting the currently authenticated user.
	 *
	 * Useful for SPAs that are using web routes/controllers
	 * to handle authentication.
	 *
	 * Since authentication would happen on these web routes,
	 * we need to expose a way once redirected to the SPA for
	 * a consumer to get the user that was just authenticated.
	 * 
	 * @param  \Illuminate\Http\Request $request
	 * 
	 * @return \Illuminate\Http\Resources\Json\JsonResource
	 */
	public function me(Request $request) : JsonResource
	{
		return $this->show(auth()->user(), $request);
	}

	/**
	 * Get the user's data.
	 * 
	 * @param  \App\User                $user    User in request URI.
	 * @param  \Illuminate\Http\Request $request
	 * 
	 * @return \Illuminate\Http\Resources\Json\JsonResource
	 */
    public function show(User $user, Request $request) : JsonResource
    {
    	return new UserResource($user);
    }
}
