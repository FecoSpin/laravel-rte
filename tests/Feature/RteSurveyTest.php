<?php

namespace Tests\Feature;

use App\Models\RteSurvey;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RteSurveyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_authenticated_user_can_create_survey()
    {
        $zone = Zone::factory()->create();
        $user = User::factory()->create([
            'zone_id' => $zone->id,
            'active' => true,
        ]);

        $response = $this->actingAs($user, 'sanctum')
                        ->postJson('/api/surveys', [
                            'title' => 'Test Survey',
                            'description' => 'Test Description',
                            'zone_id' => $zone->id,
                            'form_data' => ['test' => 'data'],
                        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'survey' => ['id', 'title', 'folio', 'status'],
                ]);

        $this->assertDatabaseHas('rte_surveys', [
            'title' => 'Test Survey',
            'user_id' => $user->id,
            'zone_id' => $zone->id,
        ]);
    }

    public function test_user_can_only_view_own_surveys()
    {
        $zone = Zone::factory()->create();
        $user1 = User::factory()->create(['zone_id' => $zone->id, 'active' => true]);
        $user2 = User::factory()->create(['zone_id' => $zone->id, 'active' => true]);

        $survey1 = RteSurvey::factory()->create(['user_id' => $user1->id, 'zone_id' => $zone->id]);
        $survey2 = RteSurvey::factory()->create(['user_id' => $user2->id, 'zone_id' => $zone->id]);

        // User1 can view their own survey
        $response = $this->actingAs($user1, 'sanctum')
                        ->getJson("/api/surveys/{$survey1->id}");
        $response->assertStatus(200);

        // User1 cannot view user2's survey
        $response = $this->actingAs($user1, 'sanctum')
                        ->getJson("/api/surveys/{$survey2->id}");
        $response->assertStatus(403);
    }

    public function test_supervisor_can_approve_surveys_in_their_zone()
    {
        $zone = Zone::factory()->create();
        $supervisor = User::factory()->create([
            'role' => 'supervisor',
            'zone_id' => $zone->id,
            'active' => true,
        ]);
        $technician = User::factory()->create([
            'role' => 'technician',
            'zone_id' => $zone->id,
            'active' => true,
        ]);

        $survey = RteSurvey::factory()->create([
            'user_id' => $technician->id,
            'zone_id' => $zone->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($supervisor, 'sanctum')
                        ->postJson("/api/surveys/{$survey->id}/approve");

        $response->assertStatus(200);
        
        $survey->refresh();
        $this->assertEquals('approved', $survey->status);
        $this->assertEquals($supervisor->id, $survey->approved_by);
    }

    public function test_technician_cannot_approve_surveys()
    {
        $zone = Zone::factory()->create();
        $technician = User::factory()->create([
            'role' => 'technician',
            'zone_id' => $zone->id,
            'active' => true,
        ]);

        $survey = RteSurvey::factory()->create([
            'user_id' => $technician->id,
            'zone_id' => $zone->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($technician, 'sanctum')
                        ->postJson("/api/surveys/{$survey->id}/approve");

        $response->assertStatus(403);
    }
}
