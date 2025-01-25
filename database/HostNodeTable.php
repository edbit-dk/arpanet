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
            $table->unsignedInteger('node_id');
        });

        DB::table((new self)->table)->insert([
            [
            'host_id' => 1, 
            'node_id' => 5,
            ]
        ]);
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

