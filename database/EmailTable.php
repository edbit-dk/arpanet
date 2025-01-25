<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

use App\System\Email\EmailModel as Email;

class EmailTable extends Email
{
    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);

        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('sender');
            $table->string('recipient');
            $table->string('subject');
            $table->string('message');
            $table->boolean('is_read')->default(0);
            $table->timestamps();
        });
    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

