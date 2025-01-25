<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

use App\System\Help\HelpModel as Help;

class HelpTable extends Help
{
    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);

        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('cmd');
            $table->string('input');
            $table->string('info');
            $table->boolean('is_user')->default(0);
            $table->boolean('is_host')->default(0);
            $table->boolean('is_guest')->default(0);
            $table->boolean('is_visitor')->default(0);
        });
    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

