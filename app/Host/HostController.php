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
        $host = false;

        if(request()->get('data')) {
            $data = request()->get('data');
        } else {
            echo 'ERROR: Host Missing.';
            exit;
        }

        if(Host::auth() == 0) {
            $host = Host::connect($data);
        }

        if(Host::auth() > 0) {
            if(Host::data()->node(Host::try($data)->id)) {
                $host = Host::connect($data);
            } else {
                $host = Host::connect($data);
            }
        } 

        if(!$host) {
            echo 'ERROR: Access Denied.';
            exit;
        } else {
            echo 'Trying...';
            exit;
        }

    }

    public function scan() 
    {
        $hosts = false;

        if(!Host::guest() && Host::auth()) {
            $hosts = Host::data()->nodes;
        } 

        if(!Host::guest() && Host::auth() == 0) {
            $hosts = Host::netstat(); 
        }

        if(!empty($hosts)) {
            echo "Searching Comlinks...\n";
            echo "Searching ARPANET...\n";
            echo "Searching Hosts...\n\n";
        } else {
            echo "ERROR: Access Denied.\n";
            exit;
        }

        foreach ($hosts as $host) {

            $access = ' ';

            if($host->user(User::auth())) {
                $access = '*';
            }

            if($host->user_id == User::auth()) {
                $access = '!';
            }
            $host_name = $host->host_name;
            
            echo <<<EOT
            $access $host_name: $host->org, $host->location\n
            EOT;
        }
        
    }

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

       Host::data()->users()->updateExistingPivot(User::id(),['last_session' => \Carbon\Carbon::now()]);
       echo "Authentication Accepted.\n";
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
        User::blocked();

        if(Host::logon($input[0],  $input[1])) {

            Host::data()->users()->updateExistingPivot(User::id(),['last_session' => \Carbon\Carbon::now()]);
            echo <<< EOT
            Password Verified. 
            Please wait while system is accessed...
            EOT;
        } else {
             // Calculate remaining attempts
             $attempts_left = Host::attempts(true);
    
             if ($attempts_left == 1) {
                 echo "!!! WARNING: LOCKOUT IMMINENT !!!\n\n";
             }
 
             // Block the user after 4 failed attempts
             if ($attempts_left == 0) {

                User::blocked(true);
                exit;

             } else {
                echo <<< EOT
                *** ACCESS DENIED ***
                Attempts Left: {$attempts_left}
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