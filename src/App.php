<?php

namespace Custom;


class App 
{
    protected static $instance = null;


    public static function getInstance(...$args)
    {
        if (!isset(self::$instance[static::class])) {
            $class = static::class;
            self::$instance[static::class] = new $class(...$args);
        }

        return self::$instance[static::class];
    }

    public static function __callStatic($method, $args) 
    {
        $instance = self::getInstance();
        return call_user_func_array([$instance, $method], $args);
    }
}