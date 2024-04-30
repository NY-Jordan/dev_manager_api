<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_user_can_create_a_project(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken("API TOKEN", ['*'])->plainTextToken;
        $payload = [
            'name' =>fake()->name
        ];
        $this->json('post', 'api/project/create', $payload, ['Authorization' => 'Bearer '.$token])
             ->assertStatus(Response::HTTP_OK)
             ->assertJsonStructure(
                 [
                        "message",
                        "status"
                 ]
             );
        $this->assertDatabaseHas('projects', $payload);
    }

    public function test_user_can_invite_a_user_in_the_project(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $project = Project::factory()->create([
            'user_id' => $user1->id
        ]);
        $token = $user1->createToken("API TOKEN", ['*'])->plainTextToken;
        $this->json('post', 'api/project/invite/'.$user2->id.'/user/'.$project->id,
        [], ['Authorization' => 'Bearer '.$token])
             ->assertStatus(Response::HTTP_CREATED)
             ->assertJsonStructure(
                 [
                    "data" => [
                        "uuid",
                        "user"=> [
                            "username",
                            "email",
                            "picture"
                        ],
                        "status"
                    ]
                 ]
             );
    }
}
