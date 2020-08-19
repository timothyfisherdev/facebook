<?php

namespace App\Http\Controllers\API\REST\v1;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Requests\StorePost;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Resources\API\REST\v1\Post as PostResource;

class PostsController extends Controller
{
	public function index(Request $request)
	{
		$posts = QueryBuilder::for(auth()->user()->posts(), $request)
			->allowedIncludes('user')
			->get();

        return ['posts' => PostResource::collection($posts)];
	}

    public function store(StorePost $request)
    {
    	$post = auth()->user()->posts()->create($request->validated());

    	return response()->json([
    		'post' => new PostResource($post)
    	], 201);
    }
}
