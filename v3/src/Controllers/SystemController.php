<?php

namespace App\Controllers;

use App\Providers\Controller;

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

        if($this->host->auth()) {
            return $this->server();
        }

        if($this->user->check()) {
            return $this->termlink();
        }

        return view('robco/welcome.txt');
    }

    public function termlink() 
    {
        $termlink = view('robco/auth.txt');
        $server_id = false;

        if($this->host->check()) {

            if($this->host->guest()) {
                $server_id = $this->host->guest();
            }

            if($this->host->auth()) {
                $server_id = $this->host->auth();
            }
            
        }


        if(!$server_id) {
            echo $termlink;
        } else {
        echo <<< EOT
        $termlink
                     -Server $server_id-

        Password Required
        EOT;
        }

        return;

    }

    public function server() 
    {
        $termlink =  view('robco/termlink.txt');
        $server_id = false;

        $server_name = $this->host->server()->name;
        $server_location = $this->host->server()->location;

        $username = auth()->user()->username;

        if($this->host->check()) {

            if($this->host->auth()) {
                $server_id = $this->host->auth();
            }
            
        }

        if(!$server_id) {
        echo $termlink;
        } else {
        echo <<< EOT
        $termlink
        $server_name - $server_location

        Welcome, $username 
        ___________________________________________
        EOT;
        }

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