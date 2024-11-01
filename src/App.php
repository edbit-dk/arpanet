<?php

namespace Lib;


class App 
{
    protected static $instance = null;


    public static function factory(...$args)
    {
        if (!isset(self::$instance[static::class])) {
            $class = static::class;
            self::$instance[static::class] = new $class(...$args);
        }

        return self::$instance[static::class];
    }

    public static function __callStatic($method, $args) 
    {
        $instance = self::factory();
        return call_user_func_array([$instance, $method], $args);
    }
}