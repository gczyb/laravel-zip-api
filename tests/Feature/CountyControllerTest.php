<?php

namespace Tests\Feature;

use App\Models\County;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountyControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_counties()
    {
        County::factory()->create(['name' => 'Pest']);
        County::factory()->create(['name' => 'Baranya']);

        $response = $this->getJson('/api/counties');

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Pest'])
            ->assertJsonFragment(['name' => 'Baranya']);
    }

    public function test_show_returns_single_county()
    {
        $county = County::factory()->create(['name' => 'Pest']);

        $response = $this->getJson("/api/counties/{$county->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Pest']);
    }

    public function test_store_creates_new_county()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/counties', [
            'name' => 'Somogy'
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Somogy']);

        $this->assertDatabaseHas('counties', ['name' => 'Somogy']);
    }

    public function test_store_requires_authentication()
    {
        $response = $this->postJson('/api/counties', [
            'name' => 'Somogy'
        ]);

        $response->assertStatus(401);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/counties', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_validates_unique_name()
    {
        County::factory()->create(['name' => 'Pest']);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/counties', [
            'name' => 'Pest'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_update_modifies_existing_county()
    {
        $county = County::factory()->create(['name' => 'Heves']);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/counties/{$county->id}", [
            'name' => 'Nógrád'
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Nógrád']);

        $this->assertDatabaseHas('counties', ['id' => $county->id, 'name' => 'Nógrád']);
    }

    public function test_update_requires_authentication()
    {
        $county = County::factory()->create(['name' => 'Heves']);

        $response = $this->putJson("/api/counties/{$county->id}", [
            'name' => 'Nógrád'
        ]);

        $response->assertStatus(401);
    }

    public function test_delete_removes_county()
    {
        $county = County::factory()->create(['name' => 'Vas']);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/counties/{$county->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'County deleted successfully']);

        $this->assertDatabaseMissing('counties', ['id' => $county->id]);
    }

    public function test_delete_requires_authentication()
    {
        $county = County::factory()->create(['name' => 'Vas']);

        $response = $this->deleteJson("/api/counties/{$county->id}");

        $response->assertStatus(401);
    }
}