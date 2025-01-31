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
            $table->string('user_name')->unique();
            $table->string('email')->unique();
            $table->string('access_code')->unique();
            $table->string('password')->nullable();
            $table->enum('role', ['standard', 'superuser', 'system', 'service'])->default('standard');
            $table->unsignedTinyInteger('level_id')->default(0);
            $table->boolean('active')->default(1);
            $table->unsignedTinyInteger('xp')->default(0);
            $table->ipAddress('ip')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamps();
        });

        DB::table((new self)->table)->insert([
            [
                'user_name' => 'root', 
                'email' => 'root@teleterm.net', 
                'password' => random_pass(),
                'access_code' => access_code(),
                'fullname' => 'Superuser',
                'role' => 'superuser',
                'level_id' => 6,
                'xp' => 100
            ],
            [
                'user_name' => 'admin', 
                'email' => 'admin@teleterm.net',
                'password' => random_pass(),
                'access_code' => access_code(),
                'fullname' => 'Administrator',
                'role' => 'superuser',
                'level_id' => 6,
                'xp' => 100
            ],
            [
                'user_name' => 'guest', 
                'email' => 'guest@teleterm.net', 
                'password' => null,
                'access_code' => access_code(),
                'fullname' => 'Guest account',
                'role' => 'standard',
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

