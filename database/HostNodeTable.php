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

        $nodes = require BASE_PATH . '/config/nodes.php';
        $chunkSize = 500; // Adjust based on server capabilities

        DB::beginTransaction();
        try {
            foreach (array_chunk($nodes, $chunkSize) as $chunk) {
                DB::table((new self)->table)->insert($chunk);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

