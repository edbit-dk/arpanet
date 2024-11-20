<?php

namespace App\Host;

use Lib\Controller;
use Lib\Session;

use App\Host\File\FileService as File;

use App\User\UserService as User;
use App\Host\HostService as Host;

class HostController extends Controller
{

    public function connect() 
    {
        $server = '';
        
        if(request()->get('data')) {
            $data = request()->get('data');
            $server = Host::connect($data);
        }

        if(empty($server)) {
            echo 'ERROR: Unknown Host';
            exit;
        } else {
            echo "Trying...";
            exit;
        }

    }

    public function dir()
    {
        File::list(Host::data()->id);
    }

    public function open()
    {
        $data = explode(' ', request()->get('data'));

        File::open($data[0], Host::data()->id);
    }

    public function echo()
    {
        $data = request()->get('data');

        $input = explode('>', $data);

        $file_content = str_replace("'", '', trim($input[0]));
        $file_name = trim($input[1]);

        $file = File::create(
            User::data()->id, 
            Host::data()->id,
            0,
            $file_name,
            $file_content
        );

        var_dump($file );
    }

    public function scan() 
    {
        $nodes = '';
        $access = '';

        if(Host::auth() OR Host::guest()) {
            $hosts = Host::data()->nodes()->get();
        } else {
            $hosts  = Host::netstat();
        }

        echo "Searching Comlinks...\n";
        echo "Searching...\n";
        echo "Searching ARPANET...\n";
        echo "Active ARPANET Stations:\n";

        foreach ($hosts as $host) {

            if(isset($host->type->name)) {
                $type = $host->type->name;
            } else {
                $type = 'UNKNOWN';
            }

            if(!empty($host->user(User::auth()))) {
                $access = '*';
            }
            
            echo "$access $host->host_name@$host->ip\n";
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

    public function mail()
    {
        
    }

    public function logoff() 
    {
        Host::logoff();
        echo "%connection closed.";
    }

}