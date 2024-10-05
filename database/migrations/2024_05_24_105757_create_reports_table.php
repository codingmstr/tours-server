<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->integer('admin_id')->default(0);
            $table->integer('vendor_id')->default(0);
            $table->integer('client_id')->default(0);
            $table->string('table')->nullable();
            $table->integer('column')->default(0);
            $table->string('process')->nullable();
            $table->string('ip')->nullable();
            $table->string('agent')->nullable();
            $table->string('location')->nullable();
            $table->float('price')->default(0);
            $table->float('amount')->default(0);
            $table->string('status')->nullable();
            $table->boolean('paid')->default(false);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
