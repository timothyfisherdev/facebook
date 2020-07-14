<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    public function show(User $user, Request $request)
    {
    	return new UserResource($user);
    }
}
