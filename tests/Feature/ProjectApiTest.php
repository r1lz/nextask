<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Project;

class ProjectApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_list_their_projects()
    {
        $user = User::factory()->create();
        Project::factory()->count(3)->create(['user_id' => $user->id]);
        
        $otherUser = User::factory()->create();
        Project::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/projects');

        $response->assertStatus(200)
                 ->assertJsonCount(3, 'data.data')
                 ->assertJsonStructure([
                     'status',
                     'message',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'name', 'description', 'tasks_count', 'created_at']
                         ],
                         'links',
                         'meta'
                     ]
                 ]);
    }

    public function test_user_can_create_project()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/projects', [
            'name' => 'New Project',
            'description' => 'A great project',
        ]);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'New Project']);
                 
        $this->assertDatabaseHas('projects', ['name' => 'New Project']);
    }
}
