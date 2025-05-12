<?php

namespace DB\Seeders;

use Illuminate\Database\Capsule\Manager as DB;
use DB\Migrations\FolderTable;

class FolderSeeder extends FolderTable
{
    /**
     * Seed the application's database.
     */
    public static function run(): void
    {
        DB::table((new self)->table)->insert([
            ['foldername' => '/', 'parent_id' => null],
            ['foldername' => 'bin', 'parent_id' => 1],
            ['foldername' => 'log', 'parent_id' => 1],
            ['foldername' => 'sys', 'parent_id' => 1],
            ['foldername' => 'home', 'parent_id' => 1],
        ]);
    }
    
}