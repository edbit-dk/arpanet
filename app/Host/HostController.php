<?php

namespace App\Host;

use App\AppController;

use App\Level\LevelModel as Level;
use App\Host\HostModel as Hosts;

use App\User\UserService as User;
use App\Host\HostService as Host;
use App\Folder\FolderService as Folder;
use App\AppService as App;

class HostController extends AppController
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
            'hostname' => $name,
            'password' =>  strtolower($admin_pass),
            'level_id' => $level->id,
            'ip' => random_ip()
        ]);

        dd($host);
    }

    public function connection()
    {
        $pwd = Folder::pwd();

        if(User::auth()) {
            $hostname = User::username();
            echo "[@$hostname]";
            exit;
        }

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

        echo '@';

    }

    public function connect() 
    {
        $host = false;
        $host_id = false;

        if($this->data) {
            $data = $this->data;
        } else {
            echo 'UNKNOWN HOST';
            exit;
        }

        if($host = Host::try($data)) {
            $host_id = $host->id;
        }

        if(Host::check()) {
            
            if(Host::data()->node($host_id) || Host::data()->host($host_id)) {
                $host = Host::connect($data);
            }
        } else {
            $host = Host::connect($data);
        }

        sleep(1);

        if(!$host) {
            echo '--CONNECTION REFUSED--';
            exit;
        } 

        if($host_id > 1) {
            $host = Host::data()->hostname;
            $ip = Host::data()->ip;

            echo <<< EOT
            Connecting...
            Trying $ip
            Connected to $host\n
            EOT;
            exit;
        }

    }

    public function hosts()
    {
        $hosts = Hosts::all();

        foreach ($hosts as $host) {
            echo <<<EOT
            $host->hostname: $host->org - $host->location\n
            EOT;
        }
    }

    public function scan() 
    {
        $hosts = false;

        if(Host::check()) {
            $hosts = Host::data()->connections();
        } else {
            $hosts = Host::random();
        }

        if($hosts->isEmpty()) {
            echo "*** ACCESS DENIED ***\n";
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
            $hostname = $host->hostname;
            
            echo <<<EOT
            $access $hostname: $host->org - $host->location\n
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

       Host::data()->users()->updateExistingPivot(User::id(),['last_session' => now()]);
       echo bootup();
       exit;
    }

    public function rlogin()
    {
        $data = parse_request('data');

        if(!empty($data)) {
            if(Host::rlogin($data)) {
                echo <<< EOT
                IDENTIFICATION VERIFIED
                EOT;
            } else {
                echo <<< EOT
                *** ACCESS DENIED ***
                EOT;
            }
        }
    }

    public function logon() 
    {
        $input = App::auth($this->data);

        // Initialize login attempts if not set
        Host::attempts();

        // Check if the user is already blocked
        Host::blocked();

        sleep(1);

        if(Host::logon($input['username'],  $input['password'])) {
            echo <<< EOT
            IDENTIFICATION VERIFIED
            EOT;
        } else {
             // Calculate remaining attempts
             $attempts_left = Host::attempts(true);
    
             if ($attempts_left == 1) {
                 echo "LOCKOUT IMMINENT\n";
             }
 
             // Block the user after 4 failed attempts
             if ($attempts_left == 0) {

                Host::blocked(true);
                exit;

             } else {
                echo <<< EOT
                IDENTIFICATION NOT RECOGNIZED BY SYSTEM
                EOT;
                exit;
             }
        }
        
    }

    public function logoff() 
    {
        Host::logoff();
        echo "--CONNECTION CLOSED--";
    }

}