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

        if($this->mainframe->remote()) {
            return $this->server();
        }

        if($this->auth->check()) {
            return $this->termlink();
        }

        return view('robco/welcome.txt');
    }

    public function termlink() 
    {
        $termlink = view('robco/termlink.txt');
        $server_id = false;

        if($this->mainframe->check()) {

            if($this->mainframe->local()) {
                $server_id = $this->mainframe->local();
            }

            if($this->mainframe->remote()) {
                $server_id = $this->mainframe->remote();
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
        $termlink =  view('robco/auth.txt');
        $server_id = false;

        $server_name = $this->mainframe->server()->name;


        if($this->mainframe->check()) {

            if($this->mainframe->remote()) {
                $server_id = $this->mainframe->remote();
            }
            
        }


        if(!$server_id) {
        echo $termlink;
        } else {
        echo <<< EOT
        $termlink
                        -Server $server_id-

        $server_name
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

        !!! KEEP ACCESS CODE AS BACKUP !!!
        
        > REGISTER <EMAIL> <PASSWORD> 
        > LOGIN <USERNAME> <PASSWORD> 
        _________________________________________
        EOT;
    
        return;
    }
}