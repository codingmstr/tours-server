<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->default(0);
            $table->integer('vendor_id')->default(0);
            $table->enum('role', [1, 2, 3]);
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->string('language')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('location')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('currency')->nullable();
            $table->string('ip')->nullable();
            $table->string('agent')->nullable();
            $table->string('notes')->nullable();
            $table->float('age')->default(0);
            $table->float('balance')->default(0);
            $table->float('salary')->default(0);
            $table->boolean('super')->default(false);
            $table->boolean('supervisor')->default(false);
            $table->boolean('allow_categories')->default(true);
            $table->boolean('allow_products')->default(true);
            $table->boolean('allow_coupons')->default(true);
            $table->boolean('allow_orders')->default(true);
            $table->boolean('allow_blogs')->default(true);
            $table->boolean('allow_reports')->default(true);
            $table->boolean('allow_contacts')->default(true);
            $table->boolean('allow_clients')->default(true);
            $table->boolean('allow_vendors')->default(true);
            $table->boolean('allow_statistics')->default(true);
            $table->boolean('allow_messages')->default(true);
            $table->boolean('allow_mails')->default(true);
            $table->boolean('allow_likes')->default(true);
            $table->boolean('allow_dislikes')->default(true);
            $table->boolean('allow_reviews')->default(true);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_replies')->default(true);
            $table->boolean('allow_login')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamp('login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
