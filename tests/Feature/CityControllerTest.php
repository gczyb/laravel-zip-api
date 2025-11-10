<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\County;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CityControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_cities()
    {
        $county = County::factory()->create();
        City::factory()->create(['name' => 'Budapest', 'county_id' => $county->id]);
        City::factory()->create(['name' => 'Debrecen', 'county_id' => $county->id]);

        $response = $this->getJson('/api/cities');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Budapest'])
            ->assertJsonFragment(['name' => 'Debrecen']);
    }

    public function test_show_returns_single_city()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['name' => 'Budapest', 'county_id' => $county->id]);

        $response = $this->getJson("/api/cities/{$city->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Budapest']);
    }

    public function test_store_creates_new_city()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $county = County::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/cities', [
            'name' => 'Szeged',
            'county_id' => $county->id
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Szeged']);

        $this->assertDatabaseHas('cities', ['name' => 'Szeged']);
    }

    public function test_store_requires_authentication()
    {
        $county = County::factory()->create();

        $response = $this->postJson('/api/cities', [
            'name' => 'Szeged',
            'county_id' => $county->id
        ]);

        $response->assertStatus(401);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/cities', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'county_id']);
    }

    public function test_store_validates_county_exists()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/cities', [
            'name' => 'Szeged',
            'county_id' => 9999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['county_id']);
    }

    public function test_update_modifies_existing_city()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['name' => 'Pécs', 'county_id' => $county->id]);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/cities/{$city->id}", [
            'name' => 'Pécs Város',
            'county_id' => $county->id
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Pécs Város']);

        $this->assertDatabaseHas('cities', ['id' => $city->id, 'name' => 'Pécs Város']);
    }

    public function test_update_requires_authentication()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['name' => 'Pécs', 'county_id' => $county->id]);

        $response = $this->putJson("/api/cities/{$city->id}", [
            'name' => 'Pécs Város',
            'county_id' => $county->id
        ]);

        $response->assertStatus(401);
    }

    public function test_delete_removes_city()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['name' => 'Győr', 'county_id' => $county->id]);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/cities/{$city->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'City deleted successfully']);

        $this->assertDatabaseMissing('cities', ['id' => $city->id]);
    }

    public function test_delete_requires_authentication()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['name' => 'Győr', 'county_id' => $county->id]);

        $response = $this->deleteJson("/api/cities/{$city->id}");

        $response->assertStatus(401);
    }
}