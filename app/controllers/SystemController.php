<?php

class SystemController 
{
    public static function version() {
        return file_get_contents(APP_STORAGE . 'text/version.txt');
    }

    public static function restart() {
        AuthController::logout();
        return "RESTARTING...";
    }

    public static function boot() {
        return include(APP_STORAGE . 'text/boot.txt');
    }

    public static function motd() {
        $code_1 = random_str(6, 'AXYZ01234679');
        $code_2 = random_str(6, 'AXYZ01234679');
        $code_3 = random_str(6, 'AXYZ01234679');
        $code_4 = random_str(6, 'AXYZ01234679');
    
        $access_code = "{$code_1}-{$code_2}-{$code_3}-{$code_4}"; 
    
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
        Poseidon Energy, stretching from the Pacific
        to the Atlantic.

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
