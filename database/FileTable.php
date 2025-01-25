<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

use App\Host\File\FileModel as File;

class FileTable extends File
{
    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);

        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('file_name');
            $table->text('content');
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
            $table->unsignedInteger('host_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->timestamps();
        });
    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

