<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory {

    public function definition () {

        $title = fake()->sentence();

        return [
            'name' => $title,
            'slug' => Str::slug($title),
            'company' => fake()->company(),
            'description' => fake()->text(70),
        ];

    }

}
