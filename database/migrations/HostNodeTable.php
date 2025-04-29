<?php

namespace DB\Migrations;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

class HostNodeTable
{
    protected $table = 'host_node';

    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
        DB::schema()->dropIfExists((new self)->table);
        
        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('host_id');
            $table->unsignedInteger('node_id');
            $table->index('host_id');
            $table->index('node_id');
            $table->unique(['host_id', 'node_id']);
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('cascade');
            $table->foreign('node_id')->references('id')->on('hosts')->onDelete('cascade');
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

