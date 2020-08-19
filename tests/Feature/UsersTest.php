<?php

namespace Tests\Feature;

use App\User;
use App\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class UsersTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_valid_client_can_retrieve_the_currently_authenticated_api_user()
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
        $response = $this->getJson('/api/rest/v1/users/me');

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)->assertJson([
            'user' => [
                'id' => $user->id,
                'name' => $user->name
            ]
        ]);
    }

    /** @test */
    public function a_user_can_fetch_other_users_data()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        [$authUser, $otherUser] = factory(User::class, 2)->create();

        $this->actingAs($authUser, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->getJson('/api/rest/v1/users/' . $otherUser->id);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)->assertJson([
            'user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name
            ]
        ]);
    }

    /** @test */
    public function a_user_can_fetch_other_users_data_including_posts()
    {
        $this->withoutExceptionHandling();
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        [$authUser, $otherUser] = factory(User::class, 2)->create();
        $posts = factory(Post::class, 2)->create(['user_id' => $otherUser->id]);

        $this->actingAs($authUser, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->getJson('/api/rest/v1/users/' . $otherUser->id . '?include=posts');

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(200)->assertJson([
            'user' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'posts' => [
                    ['id' => $posts->last()->id],
                    ['id' => $posts->first()->id]
                ]
            ]
        ]);
    }
}
