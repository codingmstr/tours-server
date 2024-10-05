<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingFactory extends Factory {

    public function definition () {

        return [
            'name' => 'Coding Master',
            'email' => 'codingmaster@gmail.com',
            'phone' => '+20 109 918 8572',
            'country' => 'Egypt',
            'city' => 'Benha',
            'location' => 'Egypt - Benha - City star',
            'language' => 'en',
            'theme' => 'dark',
            'facebook' => 'https://facebook.com',
            'youtube' => 'https://youtube.com',
            'instagram' => 'https://instagram.com',
            'linkedin' => 'https://linkedin.com',
            'twitter' => 'https://twitter.com',
            'telegram' => 'codingmaster001',
            'whatsapp' => '+2001221083507',
        ];

    }

}
