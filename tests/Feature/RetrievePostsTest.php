<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Post;

class RetrievePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_retrieve_posts()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');
        $posts = factory(Post::class, 2)->create();

        $response = $this->get('/api/posts');

        $response->assertStatus(200)->assertJson([
            'data' => [
                [
                    'data' => [
                        'type' => 'posts',
                        'id' => $posts->first()->id,
                        'attributes' => [
                            'posted_by' => [
                                'data' => [
                                    'attributes' => [
                                        'name' => $posts->first()->user->name
                                    ]
                                ]
                            ],
                            'body' => $posts->first()->body
                        ]
                    ]
                ],
                [
                    'data' => [
                        'type' => 'posts',
                        'id' => $posts->last()->id,
                        'attributes' => [
                            'posted_by' => [
                                'data' => [
                                    'attributes' => [
                                        'name' => $posts->last()->user->name
                                    ]
                                ]
                            ],
                            'body' => $posts->last()->body
                        ]
                    ]
                ]
            ],
            'links' => [
                'self' => url('/posts')
            ]
        ]);
    }
}
