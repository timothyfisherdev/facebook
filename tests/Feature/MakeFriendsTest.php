<?php

namespace Tests\Feature;

use App\User;
use App\UserRelationship;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MakeFriendsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_send_a_friend_request()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $response = $this->post("/api/users/{$user1->id}/relationships", [
            'data' => [
                'type' => 'user-relationships',
                'attributes' => [
                    'related_user_id' => $user2->id
                ]
            ]
        ]);

        $this->assertCount(1, $relationships = UserRelationship::all());
        $relationship = $relationships->first();

        $response->assertStatus(201)->assertJson([
            'data' => [
                'type' => 'user-relationships',
                'id' => $relationship->id,
                'attributes' => [
                    'requester_id' => $user1->id,
                    'requested_id' => $user2->id,
                    'type' => 'pending'
                ],
                'links' => [
                    'self' => url("/users/{$user1->id}/relationships/{$relationship->id}")
                ]
            ]
        ]);
    }

    // /** @test */
    // public function a_user_cannot_submit_multiple_friend_requests_to_the_same_person()
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

    //     $response = $this->post("/api/users/{$user1->id}/relationships", [
    //         'data' => [
    //             'type' => 'user-relationships',
    //             'attributes' => [
    //                 'related_user_id' => $user2->id
    //             ]
    //         ]
    //     ]);

    //     $response->assertStatus(409)->assertJson([
    //         'errors' => [
    //             'status' => '409',
    //             'title' => 'Invalid User Relationship'
    //         ]
    //     ]);
    // }

    // /** @test */
    // public function a_user_cannot_friend_request_themselves()
    // {
    //     $this->withoutExceptionHandling();
    //     $this->actingAs($user = factory(User::class)->create(), 'api');

    //     $response = $this->post("/api/users/{$user->id}/relationships", [
    //         'data' => [
    //             'type' => 'user-relationships',
    //             'attributes' => [
    //                 'related_user_id' => $user->id
    //             ]
    //         ]
    //     ]);

    //     $response->assertStatus(409);
    // }

    /** @test */
    public function only_valid_users_can_be_friend_requested()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');
        
        $response = $this->post("/api/users/{$user->id}/relationships", [
            'data' => [
                'type' => 'user-relationships',
                'attributes' => [
                    'related_user_id' => $invalid_user_id = 123
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
    public function users_can_accept_friend_requests()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $this->post("/api/users/{$user1->id}/relationships", [
            'data' => [
                'type' => 'user-relationships',
                'attributes' => [
                    'related_user_id' => $user2->id
                ]
            ]
        ]);

        $relationship = UserRelationship::first();

        $response = $this->actingAs($user2, 'api')
            ->patch("/api/users/{$user2->id}/relationships/{$relationship->id}", [
                'data' => [
                    'type' => 'user-relationships',
                    'id' => $relationship->id,
                    'attributes' => [
                        'type' => 'friends'
                    ]
                ]
            ]);

        $response->assertStatus(200)->assertJson([
            'data' => [
                'type' => 'user-relationships',
                'id' => $relationship->id,
                'attributes' => [
                    'requester_id' => $user1->id,
                    'requested_id' => $user2->id,
                    'type' => 'friends'
                ],
                'links' => [
                    'self' => url("/users/{$user1->id}/relationships/{$relationship->id}")
                ]
            ]
        ]);
    }

    /** @test */
    public function only_the_recipient_can_accept_a_friend_request()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $this->post("/api/users/{$user1->id}/relationships", [
            'data' => [
                'type' => 'user-relationships',
                'attributes' => [
                    'related_user_id' => $user2->id
                ]
            ]
        ]);

        $relationship = UserRelationship::first();

        $response = $this->actingAs($user3 = factory(User::class)->create(), 'api')
            ->patch("/api/users/{$user2->id}/relationships/{$relationship->id}", [
                'data' => [
                    'type' => 'user-relationships',
                    'id' => $relationship->id,
                    'attributes' => [
                        'type' => 'friends'
                    ]
                ]
            ]);

        $response->assertStatus(401)->assertJson([
            'errors' => [
                'status' => '401',
                'title' => 'Authorization Error',
                'detail' => 'Your request was not authorized.'
            ]
        ]);
    }

    /** @test */
    public function a_related_user_is_required_to_make_a_friend_request()
    {
        $this->actingAs($user = factory(User::class)->create(), 'api');

        $response = $this->post("/api/users/{$user->id}/relationships", [
            'data' => [
                'type' => 'user-relationships',
                'attributes' => [
                    'related_user_id' => ''
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
    public function a_relationship_type_is_required_to_accept_a_friend_request()
    {
        $this->actingAs($user1 = factory(User::class)->create(), 'api');
        $user2 = factory(User::class)->create();

        $this->post("/api/users/{$user1->id}/relationships", [
            'data' => [
                'type' => 'user-relationships',
                'attributes' => [
                    'related_user_id' => $user2->id
                ]
            ]
        ]);

        $relationship = UserRelationship::first();

        $response = $this->actingAs($user2, 'api')
            ->patch("/api/users/{$user2->id}/relationships/{$relationship->id}", [
                'data' => [
                    'type' => 'user-relationships',
                    'id' => $relationship->id,
                    'attributes' => [
                        'type' => ''
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
