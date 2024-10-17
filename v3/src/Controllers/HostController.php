<?php

namespace App\Controllers;

use App\Providers\Controller;

use App\Models\Host;
use App\Models\Level;

class HostController extends Controller
{
    public function connect() 
    {
        $this->logoff();

        $data = strtoupper(request()->get('data'));

        $server = host()->connect($data);

        sleep(1);
        
        if(!$server) {
            echo 'ERROR: ACCESS DENIED.';
            exit;
        } else {
            echo "Contacting Host...\n";
            exit;
        }

    }

    public function create() 
    {
        $data = request()->get('data');

        $input = explode(' ', trim($data));

        $name = $input[0];

        $level = Level::inRandomOrder()->first();

        $pass_length = rand($level->min, $level->max);
        
        $admin_pass = wordlist(config('views') . '/lists/wordlist.txt', $pass_length , 1)[0];

        $location = wordlist(config('views') . '/lists/statelist.txt', $pass_length , 1)[0];
        
        $host = Host::create([
            'name' => $name,
            'password' =>  strtolower($admin_pass),
            'level_id' => $level->id,
            'location' => $location,
            'ip' => random_ip()
        ]);

        echo 'OK';
    }

    // sysadmin571_bypas /: 
    public function sysadmin()
    {
        echo bootup();
    }

    public function logon() 
    {

        $data = request()->get('data');

        $this->host->debug();
    
        $params = explode(' ', $data);
    
        // Initialize login attempts if not set
        $this->host->attempts();
    
        // Check if the user is already blocked
        $this->host->blocked();
    
        // If no parameters provided, prompt for username
        if (empty($params)) {
            echo "ERROR: WRONG USERNAME";
            exit;
        } else {
            $username = $params[0];
        }
    
        // If both username and password provided, complete login process
        if (count($params) === 2) {
            $username = strtolower($params[0]);
            $password = strtolower($params[1]);
    
            // Validate password
            if ($this->host->logon($username, $password)) {
    
                // Reset login attempts on successful login
                $this->host->reset();
                auth()->user()->hosts()->attach(host()->guest());

                sleep(1);
                
                echo "Password Accepted.\nPlease wait while system is accessed...\n+0025 XP ";
                exit;
    
            } else {
    
                // Calculate remaining attempts
                $attempts_left = $this->host->attempts(true);
    
                if ($attempts_left === 1) {
                    echo "WARNING: LOCKOUT IMMINENT !!!\n";
                }
    
                // Block the user after 4 failed attempts
                if ($attempts_left === 0) {
                    $this->host->block(true);
                    echo "TERMINAL LOCKED.\n";
                    echo "Please contact an administrator.";
                    exit;
                }
    
                echo "ERROR: WRONG USERNAME.\nAttempts Remaining: {$attempts_left}";
                exit;
            }
        }
    }

    public function scan() 
    {
        if(host()->auth() OR host()->guest()) {
            $servers = host()->server()->nodes()->get();
        } else {
            $servers  = Host::inRandomOrder()->limit(5)->get();
        }

        echo "Scanning...\n \n";

        foreach ($servers as $server) {

            $id = $server->id;
            $org = $server->org;
            $name = $server->name;
            $location = $server->location;

            echo "$id. $name [$org] ($location)\n";
        }
        
    }

    public function logoff() {

        $this->host->logout();
        unset($_SESSION['debug_pass']);
        unset($_SESSION['debug_attempts']);
        unset($_SESSION['user_blocked']);
        unset($_SESSION['dump']);
        unset($_SESSION['root']);
        unset($_SESSION['maint']);
    }
}