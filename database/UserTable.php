<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\User\UserModel as User;

class UserTable extends User
{
    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);

        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('fullname')->nullable();
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('code')->unique();
            $table->string('password')->nullable();
            $table->unsignedTinyInteger('level_id')->default(0);
            $table->boolean('is_admin')->default(0);
            $table->boolean('active')->default(1);
            $table->unsignedTinyInteger('xp')->default(0);
            $table->ipAddress('ip')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });

        DB::table((new self)->table)->insert([
            [
                'username' => 'root', 
                'email' => 'root@teleterm.net', 
                'password' => random_pass(),
                'code' => access_code(),
                'fullname' => 'Superuser',
                'is_admin' => 0,
                'level_id' => 6,
                'xp' => 100
            ],
            [
                'username' => 'admin', 
                'email' => 'admin@teleterm.net',
                'password' => random_pass(),
                'code' => access_code(),
                'fullname' => 'Administrator',
                'is_admin' => 1,
                'level_id' => 6,
                'xp' => 100
            ],
            [
                'username' => 'guest', 
                'email' => 'guest@teleterm.net', 
                'password' => null,
                'code' => access_code(),
                'fullname' => 'Guest account',
                'is_admin' => 0,
                'level_id' => 1,
                'xp' => 0
            ]
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

