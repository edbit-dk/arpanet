<?php

/**
 * Class Auth
 * Checks if user is logged in, if not then sends the user to "yourdomain.com/login".
 * Auth::checkAuthentication() can be used in the constructor of a controller (to make the
 * entire controller only visible for logged-in users) or inside a controller-method to make only this part of the
 * application available for logged-in users.
 */
class Auth
{
    /**
     * The normal authentication flow, just check if the user is logged in (by looking into the session).
     * If user is not, then he will be redirected to login page and the application is hard-stopped via exit().
     */
    public static function check()
    {
        // initialize the session (if not initialized yet)
        Session::init();

        // self::checkSessionConcurrency();

        // if user is NOT logged in...
        // (if user IS logged in the application will not run the code below and therefore just go on)
        if (!Session::auth()) {

            // ... then treat user as "not logged in", destroy session, redirect to login page
            Session::destroy();

            return false;
        }
    }

    /**
     * The admin authentication flow, just check if the user is logged in (by looking into the session) AND has
     * user role type 7 (currently there's only type 1 (normal user), type 2 (premium user) and 7 (admin)).
     * If user is not, then he will be redirected to login page and the application is hard-stopped via exit().
     * Using this method makes only sense in controllers that should only be used by admins.
     */
    public static function role($type)
    {
        // initialize the session (if not initialized yet)
        Session::init();

        // self::checkSessionConcurrency();

        // if user is not logged in or is not an admin (= not role type 7)
        if (!Session::auth() || Session::get("role") != $type) {

            // ... then treat user as "not logged in", destroy session, redirect to login page
            Session::destroy();

            return false;
        }
    }

    public static function random($string) {
        return vsprintf('%s%s%d', [...sscanf(strtolower("$string-"), '%s %2s'), random_int(1, 99)]);
    }
}