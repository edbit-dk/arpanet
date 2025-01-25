<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

class HostNodeTable
{
    protected $table = 'host_node';

    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);
        
        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->unsignedInteger('host_id');
            $table->unsignedInteger('node_id');
        });

        Capsule::table((new self)->table)->insert([
            [
            'host_id' => 1, 
            'node_id' => 5,
            ]
        ]);
    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

