<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory {

    public function definition () {

        return [
            'product_id' => 1,
            'order_id' => 1,
            'content' => fake()->paragraph(),
            'rate' => 5
        ];

    }

}
