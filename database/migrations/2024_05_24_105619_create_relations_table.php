<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('relations', function (Blueprint $table) {
            $table->id();
            $table->integer('sender_id')->default(0);
            $table->integer('receiver_id')->default(0);
            $table->boolean('removed_sender')->default(false);
            $table->boolean('removed_receiver')->default(false);
            $table->boolean('archived_sender')->default(false);
            $table->boolean('archived_receiver')->default(false);
            $table->timestamps();
        });

    }

};
