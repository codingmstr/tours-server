<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->default(0);
            $table->integer('vendor_id')->default(0);
            $table->integer('user_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->integer('order_id')->default(0);
            $table->string('location')->nullable();
            $table->string('ip')->nullable();
            $table->string('agent')->nullable();
            $table->string('secret')->unique();
            $table->float('price')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
