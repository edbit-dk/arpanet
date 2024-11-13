<?php

namespace App\Host;

use Lib\Controller;
use Lib\Session;

use App\Host\File\FileService as File;

use App\User\UserService as User;
use App\Host\HostService as Host;

class HostController extends Controller
{

    public function index()
    {
        view('app.php');
    }

    public function connect() 
    {

        Host::logoff();
        $server = '';
        
        if(request()->get('data')) {
            $data = strtoupper(request()->get('data'));
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

        if(Host::auth() OR Host::guest()) {
            $servers = Host::data()->nodes()->get();
        } else {
            $servers  = Host::netstat();
        }

        echo "Scanning...\n";

        foreach ($servers as $server) {

            if(isset($server->type->name)) {
                $type = $server->type->name;
            } else {
                $type = 'UNKNOWN';
            }
            echo "$server->id. $server->host_name [$server->org] - $type\n";
        }
        
    }

    public function help()
    {

        $help = [];

        if(User::auth() && !Host::auth()) {
            $help = require config('path') . '/storage/array/user.php';
        }
        
        if(Host::auth()) {
            $help = require config('path') . '/storage/array/host.php';
        }

        if(Host::guest()) {
            $help = require config('path') . '/storage/array/guest.php';
        }

       if(empty($help)) {
            $help = require config('path') . '/storage/array/visitor.php';
       }
        
        $output = "HELP:\n";
        foreach ($help as $cmd => $text) {
            $output .= " $cmd $text\n";
        }
        echo $output;
    }

    public function reboot() 
    {
        echo bootup();
        view('terminal/boot.txt');
    } 
    
    public function boot() 
    {
        echo bootup();
        view('terminal/boot.txt');
    }

        // sysadmin571_bypass /: 
    public function sysadmin()
    {
        Host::data()->user(auth()->id);
        $user = Host::data()->user(User::data()->id);

       if($user) {
            Host::logon($user->user_name, $user->password);
       } else {
            User::data()->hosts()->attach(User::data()->id);

            $user = Host::data()->user(User::data()->id);
            Host::logon($user->user_name, $user->password);
       }

       echo bootup();
       echo "\nSUCCESS: Password Accepted";
       exit;
    }

    public function welcome() 
    {

        if(Host::auth()) {
            return $this->host();
        }

        if(User::auth()) {
            return $this->termlink();
        }

        view('terminal/welcome.txt');
    }

    public function termlink() 
    {

        if(Host::guest()) {
            view('terminal/auth.txt');
            
            $name = Host::data()->host_name;
            $server_ip = Host::data()->ip;
            $level = Host::data()->level->id;

            echo <<< EOT
                       -Server $server_ip-
                      
            <$name>
            Password Required             [SECURITY: $level]
            ___________________________________________
            EOT;

        } else {
            view('terminal/termlink.txt');
            exit;
        }

    }

    public function host() 
    {
        view('terminal/auth.txt');

        $server_name = Host::data()->host_name;
        $org= Host::data()->org;

        $username = User::data()->user_name;

        echo <<< EOT
                  -$server_name ($org)-

        Welcome, $username 
        __________________________________________
        EOT;

        return;

    }

    public function logon() 
    {
        $data = request()->get('data');

        $input = explode(' ', $data);

        if(Host::logon($input[0],  $input[1])) {
            echo <<< EOT
            Password Accepted. 
            Please wait while system is accessed...
            EOT;
        } else {
            echo 'ERROR: Access Denied.';
        }
        
    }

    public function logout() 
    {
        return Host::logoff();
    }

    public function version() 
    {
        view('terminal/version.txt');
    }

    public function uplink() 
    {
        $code_1 = random_str(6, 'AXYZ01234679');
        $code_2 = random_str(6, 'AXYZ01234679');
        $code_3 = random_str(6, 'AXYZ01234679');
        $code_4 = random_str(6, 'AXYZ01234679');
    
        $access_code = "{$code_1}-{$code_2}-{$code_3}-{$code_4}"; 

        Session::set('access_code', $access_code);
    
        echo <<< EOT
        
        Uplink with central ARPANET initiated.
        Security Access Code Sequence:
    
        ***********************************
        >>> {$access_code} <<<
        ***********************************

        !!! NEWUSER: BACKUP ACCESS CODE !!!

        1. Type LOGIN for authentication.
        2. Type NEWUSER to create an account.
        3. Type HELP for a command list.
        _________________________________________
        EOT;
    
        return;
    }

}