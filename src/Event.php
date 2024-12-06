<?php

namespace Lib;

class Event 
{
    public static $events = [];

    public static function dispatch($event, $data = [])
    {
        if (isset(self::$events[$event])) {
            foreach (self::$events[$event] as $listener) {
                $listener($data);
            }
        }
    }

    public static function register($event, $callback)
    {
        self::$events[$event][] = $callback;
    }
}