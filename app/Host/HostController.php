<?php

namespace App\Host;

use Lib\Controller;

use App\Level\LevelModel as Level;
use App\Host\HostModel;

use App\User\UserService as User;
use App\Host\HostService as Host;
use App\File\FileService as File;
use App\Folder\FolderService as Folder;

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

    public function connection()
    {
        $pwd = Folder::pwd();

        if(Host::guest()) {
            $hostname = Host::hostname(); 
            echo "[@$hostname]";
            exit;
        }
        
        if(Host::auth()) {
            $hostname = Host::hostname(); 
            $username = User::username();
            if(Host::data()->user_id == User::id()) {
                echo "[$username@$hostname$pwd]#";
            } else {  
                echo "[$username@$hostname$pwd]$";
            }
            exit;
        }

        echo '.';

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

        if(Host::auth() == 1) {
            $host = Host::connect($data);
        }

        if(Host::auth() > 1) {
            if(Host::data()->node(Host::try($data)->id)) {
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

        if(Host::auth() == 1) {
            $hosts = Host::netstat(); 
        }

        if(Host::auth() > 1) {
            $hosts = Host::data()->nodes;
        } 

        if(!$hosts->isEmpty()) {
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
                $access = '#';
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

    public function rlogin()
    {
        $data = parse_request('data');

        if(!empty($data)) {
            if(Host::rlogin($data)) {
                echo <<< EOT
                Account Verified. 
                Please wait while system is accessed...
                EOT;
            } else {
                echo <<< EOT
                *** ACCESS DENIED ***
                ERROR: Uknown User.
                EOT;
            }
        }
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
        echo "Session terminated.";
    }

}