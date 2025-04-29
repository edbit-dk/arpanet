<?php

namespace DB\Seeders;

use Illuminate\Database\Capsule\Manager as DB;
use DB\Migrations\LevelTable;

class LevelSeeder extends LevelTable
{
    /**
     * Seed the application's database.
     */
    public static function run(): void
    {
        DB::table((new self)->table)->insert([
            ['id' => 1, 'badge' => 'BEGINNER', 'level' => 0,'reward' => 1,'min' => 2,'max' => 3],
            ['id' => 2, 'badge' => 'NOVICE', 'level' => 15,'reward' => 2,'min' => 4,'max' => 5],
            ['id' => 3, 'badge' => 'SKILLED', 'level' => 25,'reward' => 3,'min' => 6,'max' => 8],
            ['id' => 4, 'badge' => 'ADVANCED', 'level' => 50,'reward' => 4,'min' => 8,'max' => 10],
            ['id' => 5, 'badge' => 'EXPERT', 'level' => 76,'reward' => 5,'min' => 10,'max' => 12],
            ['id' => 6, 'badge' => 'MASTER', 'level' => 100,'reward' => 10,'min' => 12,'max' => 15]
        ]);
    }

}