<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

class HostNodeTable
{
    protected $table = 'host_node';

    public static function up()
    {
        DB::schema()->dropIfExists((new self)->table);
        
        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->unsignedInteger('host_id');
            $table->foreign('host_id')->references('id')->on('hosts');
            $table->unsignedInteger('node_id');
            $table->foreign('node_id')->references('id')->on('hosts');
        });

        DB::table((new self)->table)->insert([
            ['host_id' => 2, 'node_id' => 3],
            ['host_id' => 2, 'node_id' => 4],
            ['host_id' => 2, 'node_id' => 5],
            ['host_id' => 2, 'node_id' => 6],
            ['host_id' => 7, 'node_id' => 8],
            ['host_id' => 7, 'node_id' => 9],
            ['host_id' => 10, 'node_id' => 11],
            ['host_id' => 10, 'node_id' => 12],
            ['host_id' => 10, 'node_id' => 13],
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

