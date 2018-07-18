<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('主键自增');
            $table->string('NickName')->comment('用户名');
            $table->string('OpenID')->comment('用户唯一ID');
            $table->string('Email')->nullable()->comment('用户Email');
            $table->string('Password')->nullable()->comment('用户密码');
            $table->string('Avatar')->comment('用户头像链接');
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
