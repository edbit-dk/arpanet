<?php

namespace App\Controllers;

use App\Providers\Controller;

use App\Models\Host;

class SystemController extends Controller
{

    public function reboot() 
    {
        view('robco/boot.txt');
    } 
    
    public function boot() 
    {
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
        $termlink = view('robco/termlink.txt');
        $server_id = false;

        if(host()->guest()) {
            
        $server_id = host()->server()->id;
        $server_ip = host()->server()->ip;

        echo <<< EOT
        $termlink
               -Server $server_id ($server_ip)-

        Password Required
        EOT;

        } else {
        echo $termlink;
        }

        return;

    }

    public function server() 
    {
        $termlink =  view('robco/auth.txt');

        $server_name = host()->server()->name;
        $server_location = host()->server()->location;

        $username = auth()->user()->username;

        echo <<< EOT
        $termlink
                 $server_name ($server_location)

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
        > LOGIN <USERNAME> <ACCESS CODE>
        _________________________________________
        EOT;
    
        return;
    }
}