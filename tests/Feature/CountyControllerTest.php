<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\County;

class CountyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_counties()
    {
        County::create(['name' => 'Pest']);
        County::create(['name' => 'Bács-Kiskun']);

        $response = $this->get('/api/counties');

        $response->assertStatus(200);
    }

    public function test_can_create_county_with_auth()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/counties', [
                'name' => 'Fejér'
            ]);

        $response->assertStatus(201);
    }

    public function test_cannot_create_county_without_auth()
    {
        $response = $this->postJson('/api/counties', [
            'name' => 'Fejér'
        ]);

        $response->assertStatus(401);
    }

    public function test_can_update_county_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/counties/{$county->id}", [
                'name' => 'Pest Updated'
            ]);

        $response->assertStatus(200);
    }

    public function test_can_delete_county_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/counties/{$county->id}");

        $response->assertStatus(200);
    }
}