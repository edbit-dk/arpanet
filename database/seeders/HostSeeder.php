<?php

namespace DB\Seeders;

use Illuminate\Database\Capsule\Manager as DB;
use DB\Migrations\HostTable;

use App\Host\HostModel as Host;

class HostSeeder extends HostTable
{
    /**
     * Seed the application's database.
     */
    public static function run(): void
    {
        $file = BASE_PATH . '/public/text/hosts_list.txt';
        if (!file_exists($file)) {
            die("File not found!");
        }
        $handle = fopen($file, "r");
        if (!$handle) {
            die("Cannot read file!");
        }

        // Find allerede eksisterende hosts i databasen
        $existingHosts = Host::pluck('hostname')->toArray();
        $existingHosts = array_flip($existingHosts); // Hurtigere opslag (O(1))

        $hostsToInsert = [];
        $lineNumber = 0;

        while (($line = fgets($handle)) !== false) {
            $lineNumber++;
        
            if ($lineNumber <= 2) {
                continue;
            }
        
            $line = rtrim($line);
        
            if ($line === '') {
                continue;
            }
        
            $parts = preg_split('/\s{2,}/', $line);
        
            if (count($parts) === 3) {
                list($hostVal, $orgVal, $locVal) = $parts;
        
                // Check if the host already exists in the database
                if (isset($existingHosts[$hostVal])) {
                    continue; // ignore
                }
        
                $hostsToInsert[] = [
                    'hostname' => trim($hostVal),
                    'org' => $orgVal,
                    'location' => $locVal,
                    'ip' => random_ip(),
                    'password' => random_pass(),
                    'os' => random_os(),
                    'welcome' => random_welcome(),
                    'level_id' => rand(1, 6),
                    'created_at' => random_date(),
                ];
            } else {
                echo "WARNING: Line $lineNumber could not be read: $line\n";
            }
        }
        fclose($handle);

        // Batch insert
        if (count($hostsToInsert) > 0) {
            $chunkSize = 500;

            DB::connection()->beginTransaction();
            try {
                foreach (array_chunk($hostsToInsert, $chunkSize) as $chunk) {
                    Host::insert($chunk);
                }
                DB::connection()->commit();
                echo "Import done! " . count($hostsToInsert) . " new rows imported!";
            } catch (\Exception $e) {
                DB::connection()->rollBack();
                echo "ERROR: " . $e->getMessage();
            }
        } else {
            echo "Done.";
        }

        /*
        $hosts = require BASE_PATH . '/config/hosts.php';
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
        */
    }
    
}