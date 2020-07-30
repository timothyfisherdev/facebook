<?php

namespace Tests\Feature;

use App\User;
use App\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ViewProfilesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_view_user_profiles()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->get('/api/users/' . $user->id);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'type' => 'users',
                'id' => $user->id,
                'attributes' => [
                    'name' => $user->name
                ],
                'links' => [
                    'self' => url('/users/' . $user->id)
                ]
            ]
        ]);
    }

    /** @test */
    public function a_user_profile_shows_the_profile_owners_posts()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $post = factory(Post::class)->create(['user_id' => $user->id]);

        $response = $this->get('/api/users/' . $user->id . '?include=posts');

        $response->assertStatus(200)->assertJson([
            'data' => [
                'type' => 'users',
                'id' => $user->id,
                'attributes' => [
                    'name' => $user->name
                ],
                'links' => [
                    'self' => url('/users/' . $user->id)
                ],
                'relationships' => [
                    'posts' => [
                        'data' => [
                            [
                                'type' => 'posts',
                                'id' => $post->id
                            ]
                        ]
                    ]
                ]
            ],
            'included' => [
                [
                    'type' => 'posts',
                    'id' => $post->id,
                    'attributes' => [
                        'body' => $post->body,
                        'image' => $post->image,
                        'posted_at' => $post->created_at->diffForHumans()
                    ],
                    'links' => [
                        'self' => url('/posts/' . $post->id)
                    ]
                ]
            ]
        ]);
    }
}
