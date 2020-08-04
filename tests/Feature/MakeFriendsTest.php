<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MakeFriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_friend_request()
    {
        $requester = factory(User::class)->create();
        $addressee = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        $this->post("/api/users/{$requester->id}/relationships/users", [
            'data' => [
                [
                    'type' => 'users',
                    'id' => $addressee->id
                ]
            ]
        ])->assertNoContent();

        $this->assertTrue($requester->relationships->contains($addressee));
    }

    /** @test */
    public function a_user_cannot_send_multiple_friend_requests_to_the_same_person()
    {
        $requester = factory(User::class)->create();
        $addressee = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        $this->post("/api/users/{$requester->id}/relationships/users", [
            'data' => [
                [
                    'type' => 'users',
                    'id' => $addressee->id
                ]
            ]
        ]);

        $this->post("/api/users/{$requester->id}/relationships/users", [
            'data' => [
                [
                    'type' => 'users',
                    'id' => $addressee->id
                ]
            ]
        ])->assertNoContent();

        $this->assertCount(1, $requester->relationships);
    }

    /** @test */
    public function a_user_cannot_friend_request_themselves()
    {
        $requester = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        $response = $this->post("/api/users/{$requester->id}/relationships/users", [
            'data' => [
                [
                    'type' => 'users',
                    'id' => $requester->id
                ]
            ]
        ]);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'status' => '422',
                'title' => 'Validation Error',
                'detail' => 'Your request is malformed or missing fields.'
            ]
        ]);
    }

    /** @test */
    public function only_valid_users_can_be_friend_requested()
    {
        $requester = factory(User::class)->create();

        $this->actingAs($requester, 'api');
        
        $response = $this->post("/api/users/{$requester->id}/relationships/users", [
            'data' => [
                [
                    'type' => 'users',
                    'id' => 123
                ]
            ]
        ]);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'status' => '422',
                'title' => 'Validation Error',
                'detail' => 'Your request is malformed or missing fields.'
            ]
        ]);
    }

    // /** @test */
    // public function users_can_accept_friend_requests()
    // {
    //     $requester = factory(User::class)->create();
    //     $addressee = factory(User::class)->create();
        
    //     $this->actingAs($requester, 'api');

    //     $this->post("/api/users/{$requester->id}/relationships/users", [
    //         'data' => [
    //             'type' => 'users',
    //             'id' => $addressee->id
    //         ]
    //     ]);

    //     $this->actingAs($addressee, 'api');

    //     $response = $this->patch("/api/users/{$adressee->id}/relationships/user", []);
    // }

    // /** @test */
    // public function only_the_recipient_can_accept_a_friend_request()
    // {
    //     $this->actingAs($user1 = factory(User::class)->create(), 'api');
    //     $user2 = factory(User::class)->create();

    //     $this->post("/api/users/{$user1->id}/relationships", [
    //         'data' => [
    //             'type' => 'user-relationships',
    //             'attributes' => [
    //                 'related_user_id' => $user2->id
    //             ]
    //         ]
    //     ]);

    //     $relationship = UserRelationship::first();

    //     $response = $this->actingAs($user3 = factory(User::class)->create(), 'api')
    //         ->patch("/api/users/{$user2->id}/relationships/{$relationship->id}", [
    //             'data' => [
    //                 'type' => 'user-relationships',
    //                 'id' => $relationship->id,
    //                 'attributes' => [
    //                     'type' => 'friends'
    //                 ]
    //             ]
    //         ]);

    //     $response->assertStatus(401)->assertJson([
    //         'errors' => [
    //             'status' => '401',
    //             'title' => 'Authorization Error',
    //             'detail' => 'Your request was not authorized.'
    //         ]
    //     ]);
    // }

    /** @test */
    public function an_addressee_is_required_to_make_a_friend_request()
    {
        $requester = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        $response = $this->post("/api/users/{$requester->id}/relationships/users", [
            'data' => [
                [
                    'type' => 'users',
                    'id' => ''
                ]
            ]
        ]);

        $response->assertStatus(422)->assertJson([
            'errors' => [
                'status' => '422',
                'title' => 'Validation Error',
                'detail' => 'Your request is malformed or missing fields.'
            ]
        ]);
    }
}
