<?php

namespace App\Http\Controllers\API\REST\v1;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
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
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function me(Request $request) : JsonResponse
	{
		return $this->show(auth()->id(), $request);
	}

	/**
	 * Get the user's data.
	 * 
	 * @param  string                   $userId    User in request URI.
	 * @param  \Illuminate\Http\Request $request
	 * 
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function show(string $userId, Request $request) : JsonResponse
    {
    	$user = QueryBuilder::for(User::class, $request)
    		->allowedIncludes('posts')
    		->findOrFail($userId);

    	return response()->json([
    		'user' => new UserResource($user)
    	]);
    }
}
