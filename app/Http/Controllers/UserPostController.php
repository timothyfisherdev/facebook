<?php

namespace App\Http\Controllers;

use App\User;
use App\Post;
use Illuminate\Http\Request;
use App\Http\Resources\PostCollection;

class UserPostController extends Controller
{
    public function index(string $userId, Request $request)
    {
    	$posts = Post::query();

    	if ($include = $request->query('include')) {
            if ($include !== 'user') {
                return response()->json([
                    'status' => '400',
                    'title' => 'Invalid include parameter',
                    'detail' => sprintf('Include parameter: %s is invalid.', $include)
                ], 400);
            }

            $posts->with('user');
        }

    	return new PostCollection(
    		$posts->where('user_id', $userId)->get()
    	);
    }
}
