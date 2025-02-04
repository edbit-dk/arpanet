<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Folder\FolderModel as Folder;

class FolderTable extends Folder
{
    public static function up()
    {
        DB::schema()->dropIfExists((new self)->table);

        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('folder_name');
            $table->unsignedInteger('parent_id')->nullable();
            $table->unsignedInteger('host_id')->nullable();
            $table->foreign('host_id')->references('id')->on('hosts');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        DB::table((new self)->table)->insert([
            ['folder_name' => 'home', 'host_id' => 1],
            ['folder_name' => 'log', 'host_id' => 1],
            ['folder_name' => 'bin', 'host_id' => 1],
            ['folder_name' => 'sys', 'host_id' => 1],
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

