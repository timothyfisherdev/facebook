<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;
use App\Post;

class PostsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_text_post()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $this->actingAs($user = factory(User::class)->create(), 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->post('/api/posts', [
            'data' => [
                'type' => 'posts',
                'attributes' => [
                    'body' => 'Testing body'
                ]
            ]
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $this->assertCount(1, $posts = Post::all());
        $post = $posts->first();

        $response->assertStatus(201)->assertJson([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => [
                    'body' => 'Testing body'
                ],
                'links' => [
                    'self' => url('/posts/' . $post->id)
                ]
            ]
        ]);
    }
}
