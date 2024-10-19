<?php

namespace App\Controllers;

use App\Providers\Controller;

use App\Models\Host;

class SystemController extends Controller
{

    public function help()
    {
        $command = strtoupper(request()->get('data'));

        if(!auth()->check()) {
            $help = require config('path') . '/storage/array/auth.php';
        } else {
            $help = require config('path') . '/storage/array/guest.php';
        }
    

        if (!empty($command)) {
            return isset($help[$command]) ? $help[$command] : "Command not found.";
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

        if(auth()->check()) {
            return $this->termlink();
        }

        $welcome = view('terminal/welcome.txt');

        echo $welcome;
    }

    public function termlink() 
    {
        $server_id = false;

        if(host()->guest()) {
            $auth =  view('terminal/auth.txt');
            
            $name = host()->server()->name;
            $server_ip = host()->server()->ip;
            $level = host()->server()->level->rep;

            echo <<< EOT
            $auth
                      -Server $server_ip-
                      
            $name
            Password Required         [SECURITY: $level]
            ___________________________________________
            EOT;

        } else {
            view('terminal/termlink.txt');
            exit;
        }

    }

    public function server() 
    {
        $termlink =  view('terminal/auth.txt');

        $server_name = host()->server()->name;
        $org= host()->server()->org;

        $username = auth()->user()->username;

        echo <<< EOT
        $termlink
                  -$server_name ($org)-

        Welcome, $username 
        __________________________________________
        EOT;

        return;

    }

    public function version() 
    {
        view('terminal/version.txt');
    }

    public static function uplink() {
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
        
        > NEWUSER
        > LOGON 
        _________________________________________
        EOT;
    
        return;
    }
}