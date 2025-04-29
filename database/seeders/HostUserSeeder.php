<?php

namespace DB\Seeders;

use Illuminate\Database\Capsule\Manager as DB;
use DB\Migrations\HostUserTable;

class HostUserSeeder extends HostUserTable
{
    protected $users = 'users';
    protected $hosts = 'hosts';

    /**
     * Seed the application's database.
     */
    public static function run(): void
    {
        merge_txt_files([
            BASE_PATH . '/public/text/hacker_list.txt', 
            BASE_PATH . '/public/text/name_list.txt'
            ],
            BASE_PATH . '/public/text/users_list.txt'
        );
        
        // Config
        $txtFile = BASE_PATH . '/public/text/users_list.txt'; 
        $userLines = file($txtFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $parsedUsers = [];

        foreach ($userLines as $line) {
            [$username, $fullname, $org] = array_map('trim', explode(',', $line));
          
            // Create or update user
            DB::table((new self)->users)->updateOrInsert(
                ['username' => $username],
                [
                'fullname' => $fullname, 
                'group' => 'admin', 
                'password' => random_pass(),
                'code' => access_code(),
                'level_id' => rand(1, 6),
                'created_at' =>  random_date(),
                ]
            );        
            $userId = DB::table((new self)->users)->where('username', $username)->value('id');
            $parsedUsers[] = ['id' => $userId, 'org' => $org];
        }

        // Match or add users to hosts i chunk
        DB::table((new self)->hosts)->orderBy('id')->chunk(500, function ($hosts) use ($parsedUsers) {
            foreach ($hosts as $host) {
                $hostId = $host->id;
                $hostOrg = $host->org;

                // Find users with mathcing organization
                $matchedUsers = array_filter($parsedUsers, function ($user) use ($hostOrg) {
                    return stripos($hostOrg, $user['org']) !== false;
                });

                // Use all matched users, or use one random
                if (empty($matchedUsers)) {
                    $selectedUser = $parsedUsers[array_rand($parsedUsers)];
                    $pairs = [[
                        'host_id' => $hostId,
                        'user_id' => $selectedUser['id'],
                        'password'=> random_pass(),
                        'last_session' => random_date(),
                    ]];
                } else {
                    $pairs = array_map(fn($u) => [
                        'host_id' => $hostId,
                        'user_id' => $u['id'],
                        'password'=> random_pass(),
                        'last_session' => random_date(),
                    ], $matchedUsers);
                }

                foreach ($pairs as $pair) {
                    $userId = $pair['user_id'];
                
                    // Tjek om brugeren har en email allerede
                    $hasEmail = DB::table((new self)->users)->where('id', $userId)->value('email');
                
                    if (empty($hasEmail)) {
                        // Sæt email baseret på hostens hostname
                        $username = DB::table((new self)->users)->where('id', $userId)->value('username');
                        $email = strtolower($username . '@' . $host->hostname);
                
                        DB::table((new self)->users)->where('id', $userId)->update([
                            'email' => $email
                        ]);
                    }
                }
                
                // Insert relations, no duplicates
                DB::table((new self)->table)->insertOrIgnore($pairs);
            }
        });

        echo "All hosts added users correctly!\n";
    }

}