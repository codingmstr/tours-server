<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailFactory extends Factory {

    public function definition () {

        return [
            'sender_id' => fake()->randomElement([1, 3]),
            'receiver_id' => fake()->randomElement([1, 3]),
            'title' => fake()->sentence(5),
            'description' => fake()->text(50),
            'content' => '<p>' . implode('</p><p>', fake()->paragraphs(5)) . '</p>',
        ];

    }

}
