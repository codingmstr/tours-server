<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory {

    public function definition () {

        return [
            'product_id' => 1,
            'client_id' => 5,
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'address' => fake()->address(),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'price' => fake()->numberBetween(1, 500),
            'secret_key' => Str::upper(Str::random(10)),
        ];

    }

}
