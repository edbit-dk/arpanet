<?php

namespace DB\Migrations;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\Blueprint;

use App\Help\HelpModel as Help;

class HelpTable extends Help
{
    public static function up()
    {
        DB::schema()->disableForeignKeyConstraints();
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
    }

    public static function down()
    {
        DB::schema()->drop((new self)->table);
    }
}

