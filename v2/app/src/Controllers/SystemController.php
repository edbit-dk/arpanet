<?php

namespace App\Controllers;

use App\Services\Controller;

use App\Models\User;

class SystemController extends Controller
{

    public function reboot($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/boot.txt');
    } 
    
    public function boot($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/boot.txt');
    }

    public function welcome($request, $response) 
    {

        if($this->mainframe->remote()) {
            return $this->server($request, $response);
        }

        if($this->auth->check()) {
            return $this->termlink($request, $response);
        }

        return file_get_contents($this->settings['path'] . '/app/storage/text/welcome.txt');
    }

    public function termlink($request, $response) 
    {
        $termlink = file_get_contents($this->settings['path'] . '/app/storage/text/termlink.txt');
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

    public function server($request, $response) 
    {
        $termlink = file_get_contents($this->settings['path'] . '/app/storage/text/robco.txt');
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

    public function version($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/version.txt');
    }

    public static function uplink() {
        $code_1 = random_str(6, 'AXYZ01234679');
        $code_2 = random_str(6, 'AXYZ01234679');
        $code_3 = random_str(6, 'AXYZ01234679');
        $code_4 = random_str(6, 'AXYZ01234679');
    
        $access_code = "{$code_1}-{$code_2}-{$code_3}-{$code_4}"; 
    
        echo <<< EOT
        
        Uplink with central PoseidoNet initiated.
        Enter Security Access Code Sequence:
    
        ***********************************
        >>> {$access_code} <<<
        ***********************************
        
        > REGISTER <EMAIL> <ACCESS CODE> 
        > LOGIN <EMAIL/USERNAME> <ACCESS CODE>
        _________________________________________
        EOT;
    
        return;
    }
}