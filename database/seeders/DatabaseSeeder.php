<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\User;

class DatabaseSeeder extends Seeder {

    public function run () {

        Setting::factory()->create();
        User::factory()->super()->create();

    }

}
