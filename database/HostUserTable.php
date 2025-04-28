<?php

namespace DB;

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
            $table->foreign('host_id')->references('id')->on('hosts');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->datetime('last_session')->nullable();
        });

        $accounts = require BASE_PATH . '/config/accounts.php';
        $chunkSize = 500; // Adjust based on server capabilities

        DB::beginTransaction();
        try {
            foreach (array_chunk($accounts, $chunkSize) as $chunk) {
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

