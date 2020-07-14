<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Post;

class CreatePostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_text_post()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post('/api/posts', [
            'data' => [
                'type' => 'posts',
                'attributes' => [
                    'body' => 'Testing body'
                ]
            ]
        ]);

        $posts = Post::all();
        $post = $posts->first();

        $this->assertCount(1, $posts);
        $this->assertEquals($post->user_id, $user->id);
        $this->assertEquals($post->body, 'Testing body');

        $response->assertStatus(201)->assertJson([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => [
                    'posted_by' => [
                        'data' => [
                            'attributes' => [
                                'name' => $user->name
                            ]
                        ]
                    ],
                    'body' => 'Testing body'
                ],
                'links' => [
                    'self' => url('/posts/' . $post->id)
                ]
            ]
        ]);
    }
}
