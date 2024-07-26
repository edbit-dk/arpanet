<?php

class App {

    public static function get($path = null) {
        if ($path) {
            $app = $GLOBALS['APP'];
            $path = explode('.', $path);

            foreach ($path as $bit) {
                if (isset($app[$bit])) {
                    $app = $app[$bit];
                }
            }
            return $app;
        }

        return $GLOBALS['APP'];
    }

    public static function set($name, $service) {
        if($service) {
            $GLOBALS['APP'][$name] = $service;
        }
        return false;
    }

}
