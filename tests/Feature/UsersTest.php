<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_valid_client_can_retrieve_the_currently_authenticated_api_user()
    {
        $this->withoutExceptionHandling();
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
            'data' => [
                'name' => $user->name
            ]
        ]);
    }

    /** @test */
    public function a_user_can_fetch_other_users_data()
    {
        $this->withoutExceptionHandling();
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
            'data' => [
                'name' => $otherUser->name
            ]
        ]);
    }
}
