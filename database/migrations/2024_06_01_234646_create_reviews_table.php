<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('reviews', function ( Blueprint $table ) {
            $table->id();
            $table->integer('admin_id')->default(0);
            $table->integer('vendor_id')->default(0);
            $table->integer('client_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->integer('order_id')->default(0);
            $table->longText('content')->nullable();
            $table->float('rate')->default(0);
            $table->boolean('allow')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
