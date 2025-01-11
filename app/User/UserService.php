<?php

namespace App\User;

use App\User\UserModel as User;
use Lib\Session;

class UserService 
{
    private static $auth = 'user';

    public static function data() 
    {
        if(self::auth()) {
            return User::find(self::auth());
        }
        return false;
    }

    public static function username()
    {
        if(self::data()) {
            return self::data()->user_name;
        } else {
            return false;
        }
    }

    public static function check()
    {
        return self::auth();
    }

    public static function auth() 
    {
        if(Session::has(self::$auth)) {
            return Session::get(self::$auth);
        }
        return false;
    }

    public static function blocked($block = false)
    {

        if($block) {
            Session::set('user_blocked', true);
        }

        if (Session::has('user_blocked') && !$block) {
            echo <<< EOT
            ERROR: Terminal Locked.
            Please contact an Administrator.
            EOT;
            exit;
        }

        if(!$block) {
            Session::remove('user_blocked');
        }
    }

    public static function login($emailOrUsername, $password) 
    {

        $user = User::where('email', $emailOrUsername)
                    ->orWhere('user_name', $emailOrUsername)
                    ->first();

        if (!$user) {
            return false;
        }

        if ($user->password == $password OR $user->access_code == $password) {
            Session::set(self::$auth, $user->id);
            return true;
        }

        return false;
    }

    public static function logout() 
    {
        self::data()->update(['last_login' => \Carbon\Carbon::now()]);
        Session::remove(self::$auth);
    }

    public static function count()
    {
        return User::count();
    }
    

}