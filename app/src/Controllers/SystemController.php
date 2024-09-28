<?php

namespace App\Controllers;

use App\Services\Controller;

use App\Models\User;

class SystemController extends Controller
{

    public function index($request, $response) 
    {

        return $this->view->render($response, 'terminal.twig');
    }

    public function boot($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/boot.txt');
    }

    public function welcome($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/welcome.txt');
    }

    public function terminal($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/termlink.txt');
    }

    public function version($request, $response) 
    {
        return file_get_contents($this->settings['path'] . '/app/storage/text/version.txt');
    }

    public function help($request, $response)
    {

        $command = strtoupper($request->getParam('data'));

        if($this->auth->check()) {
            $help = include($this->settings['path'] . '/app/storage/array/auth.php');
        } else {
            $help = include($this->settings['path'] . '/app/storage/array/guest.php');
        }
    

        if (!empty($command)) {
            return isset($help[$command]) ? $help[$command] : "Command not found.";
        }
        
        $output = "HELP:\n";
        foreach ($help as $cmd => $text) {
            $output .= " $cmd $text\n";
        }
        return $output;
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
    
        ###################################
        >>> {$access_code} <<<
        ###################################
        
        Please login/register user:
    
        > REGISTER <ACCESS CODE> <EMAIL>
        > LOGIN <ACCESS CODE> <EMAIL>
        _________________________________________
         
        EOT;
    
        return;
    }
}