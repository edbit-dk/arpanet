<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Email\EmailModel as Email;

class EmailTable extends Email
{
    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);

        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('sender');
            $table->string('recipient');
            $table->string('subject');
            $table->string('message');
            $table->boolean('is_read')->default(0);
            $table->datetimes();
        });
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

