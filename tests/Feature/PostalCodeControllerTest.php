<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\County;
use App\Models\PostalCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostalCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_postal_codes()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);
        PostalCode::factory()->create(['code' => '1011', 'city_id' => $city->id]);
        PostalCode::factory()->create(['code' => '1012', 'city_id' => $city->id]);

        $response = $this->getJson('/api/postal-codes');

        $response->assertStatus(200)
            ->assertJsonFragment(['code' => '1011'])
            ->assertJsonFragment(['code' => '1012']);
    }

    public function test_show_returns_single_postal_code()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);
        $postalCode = PostalCode::factory()->create(['code' => '1011', 'city_id' => $city->id]);

        $response = $this->getJson("/api/postal-codes/{$postalCode->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['code' => '1011']);
    }

    public function test_store_creates_new_postal_code()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/postal-codes', [
            'code' => '2000',
            'city_id' => $city->id
        ]);

        $response->assertStatus(201)
            ->assertJsonFragment(['code' => '2000']);

        $this->assertDatabaseHas('postal_codes', ['code' => '2000']);
    }

    public function test_store_requires_authentication()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);

        $response = $this->postJson('/api/postal-codes', [
            'code' => '2000',
            'city_id' => $city->id
        ]);

        $response->assertStatus(401);
    }

    public function test_store_validates_required_fields()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/postal-codes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code', 'city_id']);
    }

    public function test_store_validates_code_length()
    {
        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/postal-codes', [
            'code' => '12345',
            'city_id' => $city->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_store_validates_unique_code()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);
        PostalCode::factory()->create(['code' => '2000', 'city_id' => $city->id]);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/postal-codes', [
            'code' => '2000',
            'city_id' => $city->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    }

    public function test_update_modifies_existing_postal_code()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);
        $postalCode = PostalCode::factory()->create(['code' => '3000', 'city_id' => $city->id]);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->putJson("/api/postal-codes/{$postalCode->id}", [
            'code' => '3001',
            'city_id' => $city->id
        ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['code' => '3001']);

        $this->assertDatabaseHas('postal_codes', ['id' => $postalCode->id, 'code' => '3001']);
    }

    public function test_update_requires_authentication()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);
        $postalCode = PostalCode::factory()->create(['code' => '3000', 'city_id' => $city->id]);

        $response = $this->putJson("/api/postal-codes/{$postalCode->id}", [
            'code' => '3001',
            'city_id' => $city->id
        ]);

        $response->assertStatus(401);
    }

    public function test_delete_removes_postal_code()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);
        $postalCode = PostalCode::factory()->create(['code' => '4000', 'city_id' => $city->id]);

        $user = User::factory()->create();
        $token = $user->createToken('TestToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson("/api/postal-codes/{$postalCode->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Postal code deleted successfully']);

        $this->assertDatabaseMissing('postal_codes', ['id' => $postalCode->id]);
    }

    public function test_delete_requires_authentication()
    {
        $county = County::factory()->create();
        $city = City::factory()->create(['county_id' => $county->id]);
        $postalCode = PostalCode::factory()->create(['code' => '4000', 'city_id' => $city->id]);

        $response = $this->deleteJson("/api/postal-codes/{$postalCode->id}");

        $response->assertStatus(401);
    }
}