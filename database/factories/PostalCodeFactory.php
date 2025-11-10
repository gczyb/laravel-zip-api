<?php

namespace Database\Factories;

use App\Models\PostalCode;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostalCodeFactory extends Factory
{
    protected $model = PostalCode::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('####'),
            'city_id' => City::factory(),
        ];
    }
}