<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class HostUserTable
{
    protected $table = 'host_user';

    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);
        
        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->unsignedInteger('host_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamp('last_session')->nullable();
        });

    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

