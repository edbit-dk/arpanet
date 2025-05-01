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
            ['filename' => 'passwd', 'host_id' => 1, 'folder_id' => 4]
        ]);
    }

}