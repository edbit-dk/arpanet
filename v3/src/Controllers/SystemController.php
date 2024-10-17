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
        
        $output = "COMMANDS:\n";
        foreach ($help as $cmd => $text) {
            $output .= " $cmd $text\n";
        }
        echo $output;
    }

    public function reboot() 
    {
        echo bootup();
        view('robco/boot.txt');
    } 
    
    public function boot() 
    {
        echo bootup();
        view('robco/boot.txt');
    }

    public function welcome() 
    {

        if(host()->auth()) {
            return $this->server();
        }

        if(auth()->check()) {
            return $this->termlink();
        }

        $welcome = view('robco/welcome.txt');

        echo $welcome;
    }

    public function termlink() 
    {
        $server_id = false;

        if(host()->guest()) {
            $auth =  view('robco/auth.txt');
            
            $name = host()->server()->name;
            $server_ip = host()->server()->ip;
            $level = host()->server()->level->rep;

            echo <<< EOT
            $auth
                      -Server $server_ip-
                      
            $name
            Password Required          [SECURITY: $level]
            _____________________________________________
            EOT;

        } else {
            view('robco/termlink.txt');
            exit;
        }

    }

    public function server() 
    {
        $termlink =  view('robco/auth.txt');

        $server_name = host()->server()->name;
        $org= host()->server()->org;

        $username = auth()->user()->username;

        echo <<< EOT
        $termlink
                 -$server_name ($org)-

        Welcome, $username 
        ___________________________________________
        EOT;

        return;

    }

    public function version() 
    {
        view('robco/version.txt');
    }

    public static function uplink() {
        $code_1 = random_str(6, 'AXYZ01234679');
        $code_2 = random_str(6, 'AXYZ01234679');
        $code_3 = random_str(6, 'AXYZ01234679');
        $code_4 = random_str(6, 'AXYZ01234679');
    
        $access_code = "{$code_1}-{$code_2}-{$code_3}-{$code_4}"; 

        session()->set('access_code', $access_code);
    
        echo <<< EOT
        
        Uplink with central PoseidoNet initiated.
        Enter Security Access Code Sequence:
    
        ***********************************
        >>> {$access_code} <<<
        ***********************************

        !!! BACKUP ACCESS CODE !!!
        
        > NEWUSER <USERNAME> <ACCESS CODE> 
        > LOGIN <USERNAME> <PASSWORD/ACCESS CODE>
        _________________________________________
        EOT;
    
        return;
    }
}