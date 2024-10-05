<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory {

    public function definition () {

        return [
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'country' => fake()->countryCode(),
            'city' => fake()->city(),
            'ip' => fake()->ipv4(),
            'agent' => fake()->userAgent(),
            'content' => fake()->paragraph(),
        ];

    }

}
