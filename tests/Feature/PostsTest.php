<?php

namespace Tests\Feature;

use App\User;
use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $response = $this->postJson('/api/rest/v1/posts', [
            'body' => $body = 'Testing body'
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(201)->assertJson([
            'post' => [
                'body' => $body
            ]
        ]);

        $this->assertCount(1, Post::all());
        $this->assertDatabaseHas('posts', ['body' => $body]);
    }

    /** @test */
    public function a_user_can_fetch_their_posts()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $myPosts = factory(Post::class, 2)->create(['user_id' => $user->id]);
        $othersPosts = factory(Post::class, 2)->create();

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->getJson('/api/rest/v1/posts');

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)->assertExactJson([
            'posts' => [
                [
                    'id' => $myPosts->last()->id,
                    'body' => $myPosts->last()->body,
                    'image' => $myPosts->last()->image,
                    'posted_at' => $myPosts->last()->created_at->diffForHumans()
                ],
                [
                    'id' => $myPosts->first()->id,
                    'body' => $myPosts->first()->body,
                    'image' => $myPosts->first()->image,
                    'posted_at' => $myPosts->first()->created_at->diffForHumans()
                ]
            ]
        ]);
    }

    /** @test */
    public function a_user_can_fetch_their_posts_including_user_data()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $posts = factory(Post::class, 2)->create(['user_id' => $user->id]);

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->getJson('/api/rest/v1/posts?include=user');

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)->assertJson([
            'posts' => [
                [
                    'body' => $posts->last()->body,
                    'image' => $posts->last()->image,
                    'posted_at' => $posts->last()->created_at->diffForHumans(),
                    'posted_by' => [
                        'id' => $user->id,
                        'name' => $user->name
                    ]
                ],
                [
                    'body' => $posts->first()->body,
                    'image' => $posts->first()->image,
                    'posted_at' => $posts->first()->created_at->diffForHumans(),
                    'posted_by' => [
                        'id' => $user->id,
                        'name' => $user->name
                    ]
                ]
            ]
        ]);
    }
}
