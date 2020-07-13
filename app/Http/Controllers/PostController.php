<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Post;

class PostController extends Controller
{
    public function store(Request $request)
    {
    	$data = $request->validate([
    		'data.attributes.body' => ''
    	]);

    	$post = $request->user()->posts()->create($data['data']['attributes']);

    	return response([
    		'data' => [
    			'type' => 'posts',
    			'id' => $post->id,
    			'attributes' => [
    				'body' => $post->body
    			],
    			'links' => [
    				'self' => url('/posts/' . $post->id)
    			]
    		]
    	], 201);
    }
}
