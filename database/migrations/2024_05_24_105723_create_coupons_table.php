<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->default(0);
            $table->integer('vendor_id')->default(0);
            $table->integer('client_id')->default(0);
            $table->integer('category_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->string('name')->unique();
            $table->string('notes')->nullable();
            $table->float('discount')->default(0);
            $table->boolean('allow_orders')->default(true);
            $table->boolean('allow')->default(true);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
