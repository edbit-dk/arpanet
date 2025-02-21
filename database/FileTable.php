<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\File\FileModel as File;

class FileTable extends File
{
    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);

        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('filename');
            $table->longText('content')->nullable();
            $table->unsignedInteger('folder_id');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->unsignedInteger('host_id')->nullable();
            $table->foreign('host_id')->references('id')->on('hosts');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->datetimes();
        });

        DB::table((new self)->table)->insert([
            ['filename' => 'passwd', 'host_id' => 1, 'folder_id' => 4]
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

