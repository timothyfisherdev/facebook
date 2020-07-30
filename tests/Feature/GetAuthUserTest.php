<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetAuthUserTest extends TestCase
{
    use RefreshDatabase;
    
    /** @test */
    public function a_valid_client_can_retrieve_the_currently_authenticated_api_user()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->get('/api/me');

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
}
