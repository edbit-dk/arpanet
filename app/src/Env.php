<?php

/**
 * Class Environment
 *
 * Extremely simple way to get the environment, everywhere inside your application.
 * Extend this the way you want.
 */
class Env
{
    public static function get()
    {
        // if APP_ENV constant exists (set in Apache configs)
        // then return content of APPLICATION_ENV
        // else return "development"
        return (getenv('APP_ENV') ? getenv('APP_ENV') : "dev");
    }
}