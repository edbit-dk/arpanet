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
        Session::clear();
    }

}