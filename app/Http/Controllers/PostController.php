<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;
use App\Http\Resources\Post as PostResource;
use App\Http\Resources\PostCollection;

class PostController extends Controller
{
	public function index(Request $request)
	{
        $posts = $request->user()->posts;

        if ($include = $request->query('include')) {
            if ($include !== 'user') {
                return response()->json([
                    'status' => '400',
                    'title' => 'Invalid include parameter',
                    'detail' => sprintf('Include parameter: %s is invalid.', $include)
                ], 400);
            }

            $posts->load('user');
        }

		return new PostCollection($posts);
	}

    public function store(Request $request)
    {
    	$data = $request->validate([
    		'data.attributes.body' => ''
    	]);

    	$post = $request->user()->posts()->create($data['data']['attributes']);

    	return new PostResource($post);
    }
}
