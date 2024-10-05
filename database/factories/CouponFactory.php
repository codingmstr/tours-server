<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory {

    public function definition () {

        return [
            'name' => Str::upper(preg_replace('/\s/', '', fake()->name())),
            'discount' => fake()->numberBetween(1, 50),
        ];

    }

}
