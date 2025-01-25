<?php

namespace DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

use App\System\Level\LevelModel as Level;

class LevelTable extends Level
{
    public static function up()
    {
        Capsule::schema()->dropIfExists((new self)->table);

        Capsule::schema()->create((new self)->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('badge');
            $table->unsignedTinyInteger('level');
            $table->unsignedTinyInteger('reward');
            $table->unsignedTinyInteger('min');
            $table->unsignedTinyInteger('max');
        });

        Capsule::table((new self)->table)->insert([
            ['badge' => 'BEGINNER', 'level' => 0,'reward' => 1,'min' => 2,'max' => 3],
            ['badge' => 'NOVICE', 'level' => 15,'reward' => 2,'min' => 4,'max' => 5],
            ['badge' => 'SKILLED', 'level' => 25,'reward' => 3,'min' => 6,'max' => 8],
            ['badge' => 'ADVANCED', 'level' => 50,'reward' => 4,'min' => 8,'max' => 10],
            ['badge' => 'EXPERT', 'level' => 76,'reward' => 5,'min' => 10,'max' => 12],
            ['badge' => 'MASTER', 'level' => 100,'reward' => 10,'min' => 12,'max' => 15]
        ]);
    }

    public static function down()
    {
        Capsule::schema()->drop((new self)->table);
    }
}

