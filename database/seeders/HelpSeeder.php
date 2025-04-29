<?php

namespace DB\Seeders;

use Illuminate\Database\Capsule\Manager as DB;
use DB\Migrations\HelpTable;

class HelpSeeder extends HelpTable
{
    /**
     * Seed the application's database.
     */
    public static function run(): void
    {
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

}