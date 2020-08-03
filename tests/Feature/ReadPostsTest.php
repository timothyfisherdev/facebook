<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Post;

class ReadPostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_read_their_own_posts()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $posts = factory(Post::class, 2)->create(['user_id' => $user->id]);

        $response = $this->get('/api/posts?include=user');

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'type' => 'posts',
                    'id' => $posts->last()->id,
                    'attributes' => [
                        'body' => $posts->last()->body,
                        'image' => $posts->last()->image,
                        'posted_at' => $posts->last()->created_at->diffForHumans()
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $user->id
                            ],
                            'links' => [
                                'related' => url('/posts/' . $posts->last()->id . '/user')
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => url('/posts/' . $posts->last()->id)
                    ]
                ],
                [
                    'type' => 'posts',
                    'id' => $posts->first()->id,
                    'attributes' => [
                        'body' => $posts->first()->body,
                        'image' => $posts->first()->image,
                        'posted_at' => $posts->first()->created_at->diffForHumans()
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'type' => 'users',
                                'id' => $user->id
                            ],
                            'links' => [
                                'related' => url('/posts/' . $posts->first()->id . '/user')
                            ]
                        ]
                    ],
                    'links' => [
                        'self' => url('/posts/' . $posts->first()->id)
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'users',
                    'id' => $user->id,
                    'attributes' => [
                        'name' => $user->name
                    ],
                    'links' => [
                        'self' => url('/users/' . $user->id)
                    ]
                ]
            ]
        ]);
    }

    /** @test */
    public function a_user_cannot_read_others_posts()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $posts = factory(Post::class, 2)->create();

        $response = $this->get('/api/posts');

        $response->assertStatus(200)->assertExactJson([
            'data' => [],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }
}
