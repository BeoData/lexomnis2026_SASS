<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tenants_archive', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_key');
            $table->string('db_driver')->nullable();
            $table->string('db_host')->nullable();
            $table->string('db_port')->nullable();
            $table->string('db_name')->nullable();
            $table->string('db_user')->nullable();
            $table->text('db_password')->nullable();
            $table->boolean('active')->default(false);
            $table->json('meta')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tenants_archive');
    }
};
