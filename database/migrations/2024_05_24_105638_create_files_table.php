<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->enum('table', ['product', 'blog', 'mail', 'message', 'category', 'user', 'logo', 'slider']);
            $table->integer('column')->default(0);
            $table->enum('type', ['image', 'video', 'file']);
            $table->string('name')->nullable();
            $table->string('size')->nullable();
            $table->string('url')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
