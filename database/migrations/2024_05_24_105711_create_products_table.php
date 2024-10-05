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
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('type')->nullable();
            $table->string('company')->nullable();
            $table->string('language')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('location')->nullable();
            $table->longText('description')->nullable();
            $table->longText('details')->nullable();
            $table->longText('policy')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->float('old_price')->default(0);
            $table->float('new_price')->default(0);
            $table->string('notes')->nullable();
            $table->json('includes')->nullable();
            $table->integer('views')->default(0);
            $table->float('rate')->default(0);
            $table->boolean('allow_reviews')->default(true);
            $table->boolean('allow_orders')->default(true);
            $table->boolean('allow_coupons')->default(true);
            $table->boolean('allow')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->fullText(['name', 'phone', 'location', 'description', 'details']);

        });

    }

};
