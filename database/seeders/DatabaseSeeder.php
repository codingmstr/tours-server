<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\Blog;
use App\Models\Comment;
use App\Models\Reply;
use App\Models\Contact;
use App\Models\Mail;
use App\Models\Setting;

class DatabaseSeeder extends Seeder {

    public function run () {

        User::factory()->super()->create();
        Setting::factory()->create();

        // User::factory()->supervisor()->create();
        // User::factory()->admin()->create();
        // User::factory()->vendor()->create();
        // User::factory()->client()->create();
        // Category::factory()->create();
        // Product::factory()->create();
        // Coupon::factory()->create();
        // Order::factory()->create();
        // Review::factory()->create();
        // Blog::factory()->create();
        // Comment::factory()->create();
        // Reply::factory()->create();
        // Contact::factory()->create();
        // Mail::factory()->create();

    }

}
