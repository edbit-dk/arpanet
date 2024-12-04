<?php

namespace App\Host;

use Lib\Controller;

use App\System\Level\LevelModel as Level;
use App\Host\HostModel;

use App\User\UserService as User;
use App\Host\HostService as Host;
use App\Host\File\FileService as File;

class HostController extends Controller
{

    public static function create() 
    {
        $data = request()->get('data');

        $input = explode(' ', trim($data));

        $name = $input[0];

        $level = Level::inRandomOrder()->first();

        $pass_length = rand($level->min, $level->max);
        
        $admin_pass = wordlist(config('database') . '/wordlist.txt', $pass_length , 1)[0];
        
        $host = HostModel::create([
            'host_name' => $name,
            'password' =>  strtolower($admin_pass),
            'level_id' => $level->id,
            'ip' => random_ip()
        ]);

        dd($host);
    }

    public function connect() 
    {
        $server = '';

        if(request()->get('data')) {
            $data = request()->get('data');
        } else {
            echo 'ERROR: Hostname Missing.';
            exit;
        }

        if(Host::guest() OR Host::auth()) {
            $hosts = Host::data()->nodes()->get();

            foreach($hosts as $host) {
                if(Host::try($host->id)) {
                   $server = Host::connect($data);
                   break;
                }
            }

        } else {

            $hosts = Host::netstat();

            foreach($hosts as $host) {
                if(Host::try($host->id)) {
                   $server = Host::connect($data);
                   break;
                }
            }
        }

        if(empty($server)) {
            echo 'ERROR: Unknown Host';
            exit;
        } else {
            echo "Trying...";
            exit;
        }

    }

    public function scan() 
    {
        $access = '';
        $nodes = '';
        $hosts = '';

        if(Host::auth() OR Host::guest()) {
            $hosts = Host::data()->nodes()->get();
        } else {
            $hosts  = Host::netstat();
        }

        echo "Searching Comlinks...\n";
        echo "Searching...\n";
        echo "Searching ARPANET...\n";

        if(!$hosts->isEmpty()) {
            echo "Active ARPANET Hosts:\n";
        } else {
            echo "ERROR: Not Found.\n";
        }

        foreach ($hosts as $host) {

            if(!empty($host->user(User::auth()))) {
                $access = '*';
            }

            if($host->user_id == User::auth()) {
                $access = '!';
            }
            
            echo "$access [$host->host_name | $host->org]\n";
        }
        
    }

    // sysadmin571_bypass /: 
    public function sysadmin()
    {
        $host = Host::data();
        $user = User::data()->host($host->id);

       if($user) {
            Host::logon(User::username(), User::data()->password);
       } else {

            User::data()->hosts()->attach($host->id);

            Host::logon(User::username(), User::data()->password);
       }

       echo "SUCCESS: Authentication accepted.\n";
       echo bootup();
       exit;
    }

    public function logon() 
    {
        $data = request()->get('data');

        $input = explode(' ', $data);

        // Initialize login attempts if not set
        Host::attempts();

        // Check if the user is already blocked
        Host::blocked();

        if(Host::logon($input[0],  $input[1])) {
            echo <<< EOT
            SUCCESS: Password Accepted. 
            Please wait while system is accessed...
            EOT;
        } else {
             // Calculate remaining attempts
             $attempts_left = Host::attempts(true);
    
             if ($attempts_left == 1) {
                 echo "WARNING: LOCKOUT IMMINENT !!!\n";
             }
 
             // Block the user after 4 failed attempts
             if ($attempts_left == 0) {

                Host::logoff();
                Host::blocked(true);
                exit;

             } else {
                echo <<< EOT
                ERROR: Wrong Username.
                Attempts Remaining: {$attempts_left}
                EOT;
                exit;
             }
        }
        
    }

    public function logoff() 
    {
        Host::logoff();
        echo "%connection closed.";
    }

}