<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('category_id')->default(0);
            $table->integer('admin_id')->default(0);
            $table->integer('vendor_id')->default(0);
            $table->json('name')->nullable();
            $table->json('company')->nullable();
            $table->json('location')->nullable();
            $table->json('description')->nullable();
            $table->json('details')->nullable();
            $table->json('policy')->nullable();
            $table->json('meeting')->nullable();
            $table->json('rules')->nullable();
            $table->json('availability')->nullable();
            $table->json('more_info')->nullable();
            $table->json('includes')->nullable();
            $table->json('expected')->nullable();
            $table->json('days')->nullable();
            $table->json('times')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->float('old_price')->default(0);
            $table->float('new_price')->default(0);
            $table->string('notes')->nullable();
            $table->string('language')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->nullable();
            $table->integer('duration')->default(0);
            $table->integer('max_persons')->default(0);
            $table->integer('max_orders')->default(0);
            $table->integer('views')->default(0);
            $table->boolean('pay_later')->default(true);
            $table->boolean('allow_reviews')->default(true);
            $table->boolean('allow_orders')->default(true);
            $table->boolean('allow_coupons')->default(true);
            $table->boolean('allow_cancel')->default(true);
            $table->boolean('allow')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->fullText(['name', 'location', 'description', 'details']);
        });

    }

};
