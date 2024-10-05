<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory {

    public function definition () {

        return [
            'title' => fake()->sentence(5),
            'description' => fake()->text(100),
            'content' => '<p>' . implode('</p><p>', fake()->paragraphs(5)) . '</p>',
        ];

    }

}
