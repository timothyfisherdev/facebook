<?php

namespace Tests\Feature;

use App\User;
use App\FriendRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MakeFriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_friend_request()
    {
        $this->withoutExceptionHandling();
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $response = $this->post('/api/friend-requests', [
            'data' => [
                'type' => 'friend-requests',
                'attributes' => [
                    'user_id' => $user2->id 
                ]
            ]
        ]);

        $this->assertCount(1, $friendRequests = FriendRequest::all());
        $friendRequest = $friendRequests->first();

        $response->assertStatus(201)->assertJson([
            'data' => [
                'type' => 'friend-requests',
                'id' => $friendRequest->id,
                'attributes' => [
                    'requester_id' => $user1->id,
                    'requested_id' => $user2->id
                ],
                'links' => [
                    'self' => url('/friend-requests/' . $friendRequest->id)
                ]
            ]
        ]);
    }
}
