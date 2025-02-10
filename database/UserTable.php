<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\User\UserModel as User;

class UserTable extends User
{
    public static function up()
    {
        DB::connection()->disableQueryLog();

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

        $users = require BASE_PATH . '/config/users.php';
        $chunkSize = 500; // Adjust based on server capabilities

        DB::beginTransaction();
        try {
            foreach (array_chunk($users, $chunkSize) as $chunk) {
                DB::table((new self)->table)->insert($chunk);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

