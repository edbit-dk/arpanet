<?php

namespace DB\Migrations;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Host\HostModel as Host;

class HostTable extends Host
{
    public static function up()
    {

        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);
        
        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('hostname')->unique();
            $table->string('password')->nullable();
            $table->text('welcome')->nullable();
            $table->string('org')->nullable();
            $table->string('os')->nullable();
            $table->string('location')->nullable();
            $table->ipAddress('ip')->unique();
            $table->decimal('ip_num', 39,0)->unsigned();
            $table->text('motd')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('active')->default(1);
            $table->boolean('network')->default(0);
            $table->integer('level_id')->default(1);
            $table->datetimes();
        });

    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

