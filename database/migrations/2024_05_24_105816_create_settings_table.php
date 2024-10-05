<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up () {

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('email1')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone1')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('location')->nullable();
            $table->string('language')->nullable();
            $table->string('company')->nullable();
            $table->string('code')->nullable();
            $table->string('currency')->nullable();
            $table->string('theme')->nullable();
            $table->string('facebook')->nullable();
            $table->string('youtube')->nullable();
            $table->string('instagram')->nullable();
            $table->string('telegram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('linkedin')->nullable();
            $table->float('balance')->default(0);
            $table->float('profit')->default(0);
            $table->float('income')->default(0);
            $table->float('expenses')->default(0);
            $table->float('withdraws')->default(0);
            $table->float('deposits')->default(0);
            $table->boolean('allow_deposits')->default(true);
            $table->boolean('allow_withdraws')->default(true);
            $table->boolean('allow_payments')->default(true);
            $table->boolean('allow_pay_later')->default(true);
            $table->boolean('allow_mails')->default(true);
            $table->boolean('allow_messages')->default(true);
            $table->boolean('allow_notifications')->default(true);
            $table->boolean('allow_categories')->default(true);
            $table->boolean('allow_products')->default(true);
            $table->boolean('allow_coupons')->default(true);
            $table->boolean('allow_orders')->default(true);
            $table->boolean('allow_blogs')->default(true);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_replies')->default(true);
            $table->boolean('allow_reviews')->default(true);
            $table->boolean('allow_contacts')->default(true);
            $table->boolean('allow_reports')->default(true);
            $table->boolean('allow_logins')->default(true);
            $table->boolean('allow_emails')->default(true);
            $table->boolean('allow_vendors')->default(true);
            $table->boolean('allow_clients')->default(true);
            $table->boolean('running')->default(true);
            $table->longText('about')->nullable();
            $table->longText('terms')->nullable();
            $table->longText('policy')->nullable();
            $table->longText('services')->nullable();
            $table->longText('help')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

};
