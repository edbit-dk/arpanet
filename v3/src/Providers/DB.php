<?php

namespace App\Providers;

use App\Providers\Database;

use PDO;

class DB 
{
    private static $instance = null;

    private static $dsn = 'mysql:host=localhost;dbname=example';
    private static $username = 'root';
    private static $password = '';

    public static function connect($dsn, $username, $password)
    {
        self::$dsn = $dsn;
        self::$username = $username;
        self::$password = $password;
    }

    public static function getInstance()
    {
        if(!self::$instance) {
            $pdo = new PDO(self::$dsn, self::$username, self::$password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            self::$instance = new Database($pdo);
        }

        return self::$instance;
    }

    public static function __callStatic($method, $args) 
    {
        $instance = self::getInstance();
        return call_user_func_array([$instance, $method], $args);
    }
}