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
            ['filename' => 'trace.log', 'folder_id' => 3],
            ['filename' => 'config.sys', 'folder_id' => 4],
            ['filename' => 'notes.txt', 'folder_id' => 5]
        ]);
    }

}