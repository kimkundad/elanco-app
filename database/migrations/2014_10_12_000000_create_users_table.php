<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations. session_id
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('firstName');
            $table->string('lastName');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('access_token')->nullable();
            $table->string('avatar')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('birthday')->nullable();
            $table->string('zipcode')->nullable();
            $table->unsignedBigInteger('country')->default(0);
            $table->string('position')->nullable();
            $table->string('userType');
            $table->boolean('terms');
            $table->string('prefix')->nullable();
            $table->string('vetId')->nullable();
            $table->string('clinic')->nullable();
            $table->string('idcard')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->integer('status')->default(0);
            $table->string('code_user')->nullable();
            $table->double('point', 15, 2)->default(0.0);
            $table->text('session_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
