<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class UserFactory extends Factory {

    public function definition () {

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->email(),
            'password' => Hash::make('codingmaster'),
            'phone' => fake()->phoneNumber(),
            'language' => fake()->languageCode(),
            'country' => fake()->countryCode(),
            'city' => fake()->city(),
            'age' => fake()->numberBetween(18, 50),
            'ip' => fake()->ipv4(),
            'agent' => fake()->userAgent(),
            'remember_token' => Str::random(10),
        ];

    }
    public function super () {

        return $this->state(function (array $attributes) {

            return [
                'name' => 'Super Admin',
                'email' => 'super@gmail.com',
                'phone' => '+20 109 918 8572',
                'language' => 'en',
                'country' => 'EG',
                'city' => 'Cairo',
                'age' => '22',
                'role' => 1,
                'super' => true,
                'supervisor' => true,
            ];

        });

    }
    public function supervisor () {

        return $this->state(function (array $attributes) {

            return [
                'role' => 1,
                'supervisor' => true,
            ];

        });

    }
    public function admin () {

        return $this->state(function (array $attributes) {

            return [
                'role' => 1
            ];

        });

    }
    public function vendor () {

        return $this->state(function (array $attributes) {

            return [
                'role' => 2,
            ];

        });

    }
    public function client () {

        return $this->state(function (array $attributes) {

            return [
                'role' => 3,
            ];

        });

    }

}
