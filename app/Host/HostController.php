<?php

namespace App\Host;

use Lib\Controller;

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

        $data = strtoupper(request()->get('data'));

        $server = Host::connect($data);
        
        if(!$server) {
            echo 'ERROR: ACCESS DENIED.';
            exit;
        } else {
            echo "Contacting Host...";
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
            host()->server()->id,
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
            $servers  = Host::random();
        }

        echo "Scanning...\n";

        foreach ($servers as $server) {

            if(isset($server->type->name)) {
                $type = $server->type->name;
            } else {
                $type = 'UNKNOWN';
            }

            echo "$server->id. $server->host_name [$server->org] ($type)\n";
        }
        
    }

    public function help()
    {

        $help = [];

        if(auth()->check() && !host()->check()) {
            $help = require config('path') . '/storage/array/user.php';
        }
        
        if(host()->auth()) {
            $help = require config('path') . '/storage/array/host.php';
        }

        if(host()->check()) {
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

    public function welcome() 
    {

        if(host()->auth()) {
            return $this->server();
        }

        if(User::auth()) {
            return $this->termlink();
        }

        view('terminal/welcome.txt');
    }

    public function termlink() 
    {
        $server_id = false;

        if(host()->guest()) {
            view('terminal/auth.txt');
            
            $name = host()->data()->host_name;
            $server_ip = host()->data()->ip;
            $level = host()->data()->level->id;

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

    public function server() 
    {
        view('terminal/auth.txt');

        $server_name = host()->data()->host_name;
        $org= host()->data()->org;

        $username = auth()->data()->user_name;

        echo <<< EOT
                  -$server_name ($org)-

        Welcome, $username 
        __________________________________________
        EOT;

        return;

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

        session()->set('access_code', $access_code);
    
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
      
        > LOGON 
        > NEWUSER
        _________________________________________
        EOT;
    
        return;
    }

}