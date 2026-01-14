<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_key')->unique();
            $table->string('db_driver')->default('mysql');
            $table->string('db_host')->default('127.0.0.1');
            $table->string('db_port')->default('3306');
            $table->string('db_name');
            $table->string('db_user');
            $table->text('db_password'); // encrypted
            $table->boolean('active')->default(true);
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants');
    }
};
