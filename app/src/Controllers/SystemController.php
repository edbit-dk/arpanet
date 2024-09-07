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
        return file_get_contents(__DIR__ . '/../../storage/text/boot.txt');
    }

    public function welcome($request, $response) 
    {
        echo <<< EOT

        WELCOME TO ROBCO INDUSTRIES (TM) TERMLINK
    

        EOT;
    
        return;
    }

    public static function help() { 
        echo <<< EOT

        WELCOME TO POSEIDON ENERGY CORPORATION
        -Begin your Odyssey with us-
    
        This terminal allows access to PoseidoNET,
        the US transcontinental network operated by
        Poseidon Energy, stretching from the US Pacific
        to the US Atlantic.

        Enter UPLINK to access central PoseidoNet.
        _________________________________________
    
        EOT;
    
        return;
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