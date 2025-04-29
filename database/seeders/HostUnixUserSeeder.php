<?php

namespace DB\Seeders;

use Illuminate\Database\Capsule\Manager as DB;

class HostUnixUserSeeder
{
    protected $users = 'users';
    protected $hosts = 'hosts';
    protected $pivot = 'host_user';

    /**
     * Seed the application's database.
     */
    public static function run(): void
    {
        // Config
        $txtFile = BASE_PATH . '/public/text/unix_list.txt'; 
        $userChunkSize = 50;
        $hostChunkSize = 500;

        // Read file
        $lines = file($txtFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $userChunks = array_chunk($lines, $userChunkSize);

        // Process users in chunks
        foreach ($userChunks as $userChunk) {
            DB::connection()->transaction(function () use ($userChunk, $hostChunkSize) {
                foreach ($userChunk as $line) {
                    [$username, $fullname, $group] = array_map('trim', explode(',', $line));

                    // Create or update user
                    DB::table((new self)->users)->updateOrInsert(
                        ['username' => $username],
                        [
                        'fullname' => $fullname, 
                        'group' => $group, 
                        'password' => random_pass(),
                        'code' => access_code(),
                        'level_id' => rand(1, 6),
                        'created_at' =>  random_date(),
                        ]
                    );

                    // Get user-ID
                    $userId = DB::table((new self)->users)->where('username', $username)->value('id');

                    // Chunk hosts and add relations
                    DB::table((new self)->hosts)->select('id', 'hostname')->orderBy('id')->chunk($hostChunkSize, function ($hostChunk) use ($userId, $username) {
                        $insertData = [];

                        foreach ($hostChunk as $host) {
                            $insertData[] = [
                                'host_id' => $host->id,
                                'user_id' => $userId,
                                'password'=> random_pass(),
                                'last_session' => random_date(),
                            ];

                            DB::table((new self)->users)->where('id', $userId)->update(['email' => $username . '@' . $host->hostname]);
                        }

                        // Avoid duplicates with insertOrIgnore
                        DB::table((new self)->pivot)->insertOrIgnore($insertData);
                    });
                }
            });
        }

        echo "UNIX users importet and added to all hosts!\n";
    }
}