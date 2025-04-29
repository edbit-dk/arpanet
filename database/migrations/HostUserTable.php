<?php

namespace DB\Migrations;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

class HostUserTable
{
    protected $table = 'host_user';

    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);
        
        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('host_id');
            $table->unsignedInteger('user_id');
            $table->index('host_id');
            $table->index('user_id');
            $table->unique(['host_id', 'user_id']);
            $table->foreign('host_id')->references('id')->on('hosts');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('password')->nullable();
            $table->datetime('last_session')->nullable();
        });
    }

    public static function down()
    {
        DB::table((new self)->table, function (Blueprint $table) {
            $table->dropUnique(['host_id', 'user_id']);
            $table->dropIndex(['host_id']);
            $table->dropIndex(['user_id']);
        });

        DB::schema()->drop((new self)->table);
    }
}

