<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Help\HelpModel as Help;

class HelpTable extends Help
{
    public static function up()
    {
        DB::schema()->dropIfExists((new self)->table);

        DB::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('cmd');
            $table->string('input')->nullable();
            $table->text('info');
            $table->boolean('is_user')->default(0);
            $table->boolean('is_host')->default(0);
            $table->boolean('is_visitor')->default(0);
            $table->boolean('is_guest')->default(0);
        });

        $hosts = require BASE_PATH . '/config/help.php';
        $chunkSize = 500; // Adjust based on server capabilities

        DB::beginTransaction();
        try {
            foreach (array_chunk($hosts, $chunkSize) as $chunk) {
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

