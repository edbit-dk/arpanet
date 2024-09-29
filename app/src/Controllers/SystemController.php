<?php

namespace App\Controllers;

use App\Services\Controller;

use App\Models\User;

class SystemController extends Controller
{
    public function boot($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/boot.txt');
    }

    public function welcome($request, $response) 
    {
        if($this->auth->check()) {
            return $this->termlink($request, $response);
        }

        if($this->mainframe->check()) {
            return $this->mainframe($request, $response);
        }

        return file_get_contents($this->settings['path'] . '/app/storage/text/welcome.txt');
    }

    public function termlink($request, $response) 
    {
        $termlink = file_get_contents($this->settings['path'] . '/app/storage/text/termlink.txt');

        if($request->getParam('query')) {
            $server_id = parse_request($request->getParam('query'))['server'];
        } else {
            $server_id = false;
        }

        if(!$server_id) {
        echo "$termlink";
        } else {
        echo <<< EOT
        $termlink
                     -Server $server_id-
        EOT;
        }

        return;

    }

    public function mainframe($request, $response) 
    {
        $termlink = file_get_contents($this->settings['path'] . '/app/storage/text/mainframe.txt');

        
        if($request->getParam('query')) {
            $server_id = parse_request($request->getParam('query'))['server'];
        } else {
            $server_id = false;
        }

        $user = $this->auth->user()->username;

        if(!$server_id) {
        echo "$termlink";
        } else {
        echo <<< EOT
        $termlink
                     -Server $server_id-
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
        
        > REGISTER <ACCESS CODE> <EMAIL>
        > LOGIN <ACCESS CODE> <EMAIL/USERNAME>
        _________________________________________
        EOT;
    
        return;
    }
}