<?php

namespace App\Http\Controllers\API\REST\v1;

use App\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostCollection;
use App\Http\Resources\Post as PostResource;

class PostsController extends Controller
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
