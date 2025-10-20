<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\County;
use App\Models\City;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_cities()
    {
        $county = County::create(['name' => 'Pest']);
        City::create(['name' => 'Budapest', 'county_id' => $county->id]);

        $response = $this->get('/api/cities');

        $response->assertStatus(200);
    }

    public function test_can_create_city_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/cities', [
                'name' => 'Debrecen',
                'county_id' => $county->id
            ]);

        $response->assertStatus(201);
    }

    public function test_cannot_create_city_without_auth()
    {
        $county = County::create(['name' => 'Pest']);

        $response = $this->postJson('/api/cities', [
            'name' => 'Debrecen',
            'county_id' => $county->id
        ]);

        $response->assertStatus(401);
    }

    public function test_can_update_city_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);
        $city = City::create(['name' => 'Budapest', 'county_id' => $county->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/cities/{$city->id}", [
                'name' => 'Budapest Updated',
                'county_id' => $county->id
            ]);

        $response->assertStatus(200);
    }

    public function test_can_delete_city_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);
        $city = City::create(['name' => 'Budapest', 'county_id' => $county->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/cities/{$city->id}");

        $response->assertStatus(200);
    }
}