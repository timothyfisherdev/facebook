<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;
use Spatie\QueryBuilder\QueryBuilder;

class PostController extends Controller
{
	public function index(Request $request)
	{
        $posts = QueryBuilder::for(Post::class)
            ->allowedIncludes(['user'])
            ->where('user_id', auth()->id())
            ->get();
        
        return new PostCollection($posts);
	}

    public function store(Request $request)
    {
    	$data = $request->validate([
    		'data.attributes.body' => ''
    	]);

    	$post = auth()->user()->posts()->create($data['data']['attributes']);

    	return new PostResource($post);
    }
}
