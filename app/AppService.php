<?php

namespace App;

use Lib\Session;
use Lib\Input;

class AppService
{
    public static function version() 
    {
        echo text('version.txt');
    }

    public static function auth($input)
    {
        $input = explode(' ', $input);

        if (isset($input[0])) {
            $username = Input::sanitize($input[0]);
        } 

        if(empty($input[1])) {
            $password = '';
        } else {
            $password = Input::sanitize($input[1]);
        }

        return ['username' => $username, 'password' => $password];
    }
}