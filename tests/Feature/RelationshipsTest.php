<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RelationshipsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp() : void
    {
        parent::setUp();

        $this->seed('RelationshipStatusCodesSeeder');
    }

    /** @test */
    public function a_user_can_request_a_relationship_with_another_user()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();
        $addressee = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $addressee->id 
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertNoContent();

        $this->assertCount(1, $relationships = $requester->relationships);
        $this->assertCount(1, $relationshipsWithStatus = $requester->relationshipsWithStatus);
        $this->assertTrue($relationships->contains($addressee));
        $this->assertTrue($relationshipsWithStatus->contains($addressee));
        $this->assertEquals($relationshipsWithStatus->first()->pivot->status_code, 'R');
    }

    /** @test */
    public function a_user_cannot_request_a_relationship_on_someone_elses_behalf()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $innocentUser = factory(User::class)->create();
        $maliciousUser = factory(User::class)->create();

        $this->actingAs($maliciousUser, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->postJson("/api/rest/v1/users/{$innocentUser->id}/relationships", [
            'user_id' => $maliciousUser->id 
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(403);

        $this->assertEmpty($innocentUser->relationships);
    }

    /** @test */
    public function a_user_id_is_required_to_make_a_relationship_request()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();

        $this->actingAs($requester, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", []);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(422)->assertJsonValidationErrors([
            'user_id' => 'The user id field is required.'
        ]);
    }

    /** @test */
    public function a_user_can_only_send_relationship_requests_to_existing_users()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();

        $this->actingAs($requester, 'api');
        
        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $invalidUserId = 123
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(422)->assertJsonValidationErrors([
            'user_id' => 'The selected user id does not exist.'
        ]);
    }

    /** @test */
    public function a_user_cannot_request_a_relationship_with_themselves()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $response = $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $requester->id
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(422)->assertJsonValidationErrors([
            'user_id' => 'The selected user id cannot be your own.'
        ]);
    }

    /** @test */
    public function a_user_cannot_request_multiple_relationships_with_the_same_person()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();
        $addressee = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $addressee->id 
        ]);

        $response = $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $addressee->id
        ]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(422)->assertJsonValidationErrors([
            'user_id' => 'A relationship already exists with the selected user id.'
        ]);
    }

    /** @test */
    public function a_user_can_accept_a_relationship_request()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();
        $addressee = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $addressee->id 
        ]);

        // Set time to 1 second in the future so that the primary key in the user_relationships_status table is unique (it's a composite key between requester_id, addressee_id, and created_at). Without this the test will run too fast and the timestamp will collide with the previous POST request in this test.
        Carbon::setTestNow(now()->addSeconds(1));

        $this->actingAs($addressee, 'api');

        $response = $this->putJson("/api/rest/v1/users/{$addressee->id}/relationships/{$requester->id}/accept", [], ['Content-Length' => 0]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertNoContent();

        $this->assertCount(1, $relationships = $requester->relationships);
        $this->assertCount(1, $addressee->relationships);
        $this->assertCount(2, $relationshipsWithStatus = $requester->relationshipsWithStatus);
        $this->assertTrue($relationships->contains($addressee));
        $this->assertTrue($relationshipsWithStatus->contains($addressee));
        $this->assertEquals($relationshipsWithStatus->last()->pivot->status_code, 'A');
    }

    /** @test */
    public function a_user_can_decline_a_relationship_request()
    {
        $this->withoutExceptionHandling();
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();
        $addressee = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $addressee->id 
        ]);

        Carbon::setTestNow(now()->addSeconds(1));

        $this->actingAs($addressee, 'api');

        $response = $this->deleteJson("/api/rest/v1/users/{$addressee->id}/relationships/{$requester->id}/decline");

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertNoContent();

        $this->assertCount(2, $relationshipsWithStatus = $requester->relationshipsWithStatus);
        $this->assertEquals($relationshipsWithStatus->last()->pivot->status_code, 'D');
    }

    /** @test */
    public function only_the_recipient_of_a_relationship_request_can_accept_it()
    {
        /*
        |--------------------------------------------------------------------------
        | Arrange
        |--------------------------------------------------------------------------
        */
        $requester = factory(User::class)->create();
        $addressee = factory(User::class)->create();
        
        $this->actingAs($requester, 'api');

        /*
        |--------------------------------------------------------------------------
        | Act
        |--------------------------------------------------------------------------
        */
        $this->postJson("/api/rest/v1/users/{$requester->id}/relationships", [
            'user_id' => $addressee->id 
        ]);

        $response = $this->putJson("/api/rest/v1/users/{$requester->id}/relationships/{$addressee->id}/accept", [], ['Content-Length' => 0]);

        /*
        |--------------------------------------------------------------------------
        | Assert
        |--------------------------------------------------------------------------
        */
        $response->assertStatus(403);

        $this->assertEquals($addressee->relationshipsWithStatus()->first()->pivot->status_code, 'R');
    }
}
