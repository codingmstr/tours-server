<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory {

    public function definition () {

        return [
            'category_id' => 1,
            'name' => fake()->name(),
            'old_price' => fake()->numberBetween(500, 1000),
            'new_price' => fake()->numberBetween(1, 500),
            'description' => fake()->text(100),
            'details' => fake()->text(400),
        ];

    }

}
