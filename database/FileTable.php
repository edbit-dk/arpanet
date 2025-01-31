<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Host\File\FileModel as File;

class FileTable extends File
{
    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);

        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->longText('content');
            $table->unsignedInteger('folder_id');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->unsignedInteger('host_id')->nullable();
            $table->foreign('host_id')->references('id')->on('hosts');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });

        DB::table((new self)->table)->insert([
            ['file_name' => 'passwd', 'host_id' => 1, 'folder_id' => 4],
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

