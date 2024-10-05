<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->default(0);
            $table->integer('vendor_id')->default(0);
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('company')->nullable();
            $table->string('phone')->nullable();
            $table->string('language')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('location')->nullable();
            $table->longText('description')->nullable();
            $table->string('notes')->nullable();
            $table->boolean('allow_products')->default(true);
            $table->boolean('allow_orders')->default(true);
            $table->boolean('allow_coupons')->default(true);
            $table->boolean('allow_reviews')->default(true);
            $table->boolean('allow')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
