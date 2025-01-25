<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

use App\User\UserModel as User;

class UserTable extends User
{
    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);

        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name')->unique();
            $table->string('email')->unique();
            $table->string('access_code')->unique();
            $table->string('password');
            $table->string('firstname')->nullable();;
            $table->string('lastname')->nullable();;
            $table->boolean('active')->default(1);
            $table->integer('level_id')->default(0);
            $table->integer('xp')->default(0);
            $table->ipAddress('ip')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });

        Capsule::table((new self)->table)->insert([
            [
            'user_name' => 'root', 
            'email' => 'root@teleterm.net', 
            'access_code' => access_code(),
            'password' => base64_encode(word_pass())
            ]
        ]);
    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

