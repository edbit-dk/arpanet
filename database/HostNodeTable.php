<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Host\HostModel as Host;

class HostNodeTable
{
    protected $table = 'host_node';

    public static function up()
    {
        set_time_limit(0);
        
        DB::schema()->dropIfExists((new self)->table);
        
        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('host_id');
            $table->unsignedInteger('node_id');
            $table->unique(['host_id', 'node_id']);
            $table->foreign('host_id')->references('id')->on('hosts')->onDelete('cascade');
            $table->foreign('node_id')->references('id')->on('hosts')->onDelete('cascade');
        });

        // Batch insert
        $chunkSize = 500;
        $relations = Host::relations();

        DB::connection()->beginTransaction();
            try {
                foreach (array_chunk($relations, $chunkSize) as $chunk) {
                    DB::table((new self)->table)->insert($chunk);
                }
                    DB::connection()->commit();
                    echo "Relations created: " . count($relations) . "\n";
            } catch (\Exception $e) {
                    DB::connection()->rollBack();
                    echo "EROOR: " . $e->getMessage();
            }        

        /*
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
        */

        
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

