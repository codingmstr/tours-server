<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->integer('sender_id')->default(0);
            $table->integer('receiver_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->enum('type', ['text', 'file']);
            $table->longText('content')->nullable();
            $table->boolean('removed_sender')->default(false);
            $table->boolean('removed_receiver')->default(false);
            $table->boolean('star_sender')->default(false);
            $table->boolean('star_receiver')->default(false);
            $table->boolean('readen')->default(false);
            $table->timestamp('readen_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
