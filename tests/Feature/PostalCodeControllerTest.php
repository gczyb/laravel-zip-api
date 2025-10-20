<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\County;
use App\Models\City;
use App\Models\PostalCode;

class PostalCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_postal_codes()
    {
        $county = County::create(['name' => 'Pest']);
        $city = City::create(['name' => 'Budapest', 'county_id' => $county->id]);
        PostalCode::create(['code' => '1011', 'city_id' => $city->id]);

        $response = $this->get('/api/postal-codes');

        $response->assertStatus(200);
    }

    public function test_can_create_postal_code_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);
        $city = City::create(['name' => 'Budapest', 'county_id' => $county->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/postal-codes', [
                'code' => '1012',
                'city_id' => $city->id
            ]);

        $response->assertStatus(201);
    }

    public function test_cannot_create_postal_code_without_auth()
    {
        $county = County::create(['name' => 'Pest']);
        $city = City::create(['name' => 'Budapest', 'county_id' => $county->id]);

        $response = $this->postJson('/api/postal-codes', [
            'code' => '1012',
            'city_id' => $city->id
        ]);

        $response->assertStatus(401);
    }

    public function test_can_update_postal_code_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);
        $city = City::create(['name' => 'Budapest', 'county_id' => $county->id]);
        $postalCode = PostalCode::create(['code' => '1011', 'city_id' => $city->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/postal-codes/{$postalCode->id}", [
                'code' => '1013',
                'city_id' => $city->id
            ]);

        $response->assertStatus(200);
    }

    public function test_can_delete_postal_code_with_auth()
    {
        $user = User::factory()->create();
        $county = County::create(['name' => 'Pest']);
        $city = City::create(['name' => 'Budapest', 'county_id' => $county->id]);
        $postalCode = PostalCode::create(['code' => '1011', 'city_id' => $city->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/postal-codes/{$postalCode->id}");

        $response->assertStatus(200);
    }
}