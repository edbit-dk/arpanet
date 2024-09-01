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
        return require(APP_CONTROLLER. 'motd.php');
    }
}
