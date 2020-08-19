<?php

namespace App\Http\Controllers\API\REST\v1;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Requests\StorePost;
use App\Http\Controllers\Controller;
use App\Http\Resources\API\REST\v1\Post as PostResource;

class PostsController extends Controller
{
	public function index(Request $request)
	{
        return PostResource::collection(auth()->user()->posts);
	}

    public function store(StorePost $request)
    {
        return new PostResource(auth()->user()->posts()->create($request->validated()));
    }
}
