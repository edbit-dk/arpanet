<?php

namespace DB\Seeders;

use Illuminate\Database\Capsule\Manager as DB;
use DB\Migrations\FileTable;

class FileSeeder extends FileTable
{
    /**
     * Seed the application's database.
     */
    public static function run(): void
    {
        DB::table((new self)->table)->insert([
            ['foldername' => 'home', 'host_id' => 1],
            ['foldername' => 'log', 'host_id' => 1],
            ['foldername' => 'bin', 'host_id' => 1],
            ['foldername' => 'sys', 'host_id' => 1],
        ]);
    }

}