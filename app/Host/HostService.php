<?php

namespace App\Host;

use App\Host\HostModel as Host;
use App\User\UserModel as User;
use App\Level\LevelModel as Level;

use Lib\Session;

class HostService 
{

    private static $auth = 'host';
    private static $guest = 'guest';
    private static $max_attempts = 4; // Maximum number of allowed login attempts

    public static function data() 
    {
        if(self::guest()) {
            return Host::find(self::guest());
        }

        if(self::auth()) {
            return Host::find(self::auth());
        }

        return false;

    }

    public function hostname()
    {
        if(self::data()) {
            return self::data()->host_name;
        } else {
            return '.';
        }
    }

    public static function random($limit = 5) 
    {
        return Host::inRandomOrder()->limit($limit)->get();
    }

    public static function netstat() 
    {
        return Host::where('ip','0.0.0.0')->orWhere('ip','1.1.1.1')->get();
    }

    public static function create() 
    {
        $data = request()->get('data');

        $input = explode(' ', trim($data));

        $name = $input[0];

        $level = Level::inRandomOrder()->first();

        $pass_length = rand($level->min, $level->max);
        
        $admin_pass = wordlist(config('views') . '/lists/wordlist.txt', $pass_length , 1)[0];
        
        $host = Host::create([
            'host_name' => $name,
            'password' =>  strtolower($admin_pass),
            'level_id' => $level->id,
            'ip' => random_ip()
        ]);

        echo 'OK';
    }

    public static function check() 
    {
        if(self::auth() OR self::guest()) {
            return true;
        }

        return false;
    }

    public static function admin() 
    {
        $user = self::data()->user_id;
        return self::data()->user($user);
    }

    public static function auth()
    {
        if(Session::has(self::$auth)) {
            return Session::get(self::$auth);
        }
        return false;
    }

    public static function guest()
    {
        if(Session::has(self::$guest)) {
            return Session::get(self::$guest);
        }
        return false;
    }

    public static function connect($data)
    {
        $host = Host::where('id', $data)
        ->orWhere('ip', $data)
        ->orWhere('host_name', $data)
        ->where('active', 1)
        ->first();

        if (empty($host)) {
            return false;
        } else {
           Session::set(self::$guest, $host->id);
           return true;
        }

    }

    public static function logon($username, $password) {

        $host = false;
        $user = false;
        $host_id = self::data()->id;

        $host = Host::where('id',  $host_id)
            ->where('password', $password)
            ->first();

        if(!$host) {
            $user = User::where('user_name', $username)
            ->where('password', $password)->first();
    
            if(!$user) {
               return false;
            }

            $user = self::data()->user($user->id);

            if(!$user) {
                return false;
             }
        }
        Session::set(self::$guest, false);
        Session::set(self::$auth, $host_id);
        return true;
    }

    public static function attempt($host_id)
    {
        if(isset($host_id)) {
            Session::set(self::$guest, false);
            Session::set(self::$auth, $host_id);
            return self::data();
        }
        return false;
    }

    public static function debug($pass, $user) 
    {
        $host_id = self::data()->id;

        if(self::data()->user($user)) {
            self::attempt($host_id);
            return true;
        }

        return false;
    }

    public static function attempts($attempt = false)
    {
        if(!Session::has('logon_attempts')) {
            Session::set('logon_attempts', self::$max_attempts);
        }

        if($attempt) {
            $attempts = Session::get('logon_attempts');
            Session::set('logon_attempts', $attempts--);
        }

        return Session::get('logon_attempts');
    }

    public static function reset()
    {
        Session::remove('logon_attempts');
        Session::remove('user_blocked');
    }

    public static function blocked($block = false)
    {
        if (!Session::has('user_blocked')) {
            Session::set('user_blocked', false);
        }

        if (Session::has('user_blocked')) {
            echo "ERROR: TERMIAL LOCKED. Please contact an Administrator!";
        }

        if($block) {
            Session::set('user_blocked', true);
        }
    }

    public static function logoff() 
    {
        unset($_SESSION['debug_pass']);
        unset($_SESSION['debug_attempts']);
        unset($_SESSION['user_blocked']);
        unset($_SESSION['dump']);
        unset($_SESSION['root']);
        unset($_SESSION['maint']);

        if(self::auth()) {
            Session::remove(self::$auth); 
            return;
        }  
        
        if(!self::auth() && self::guest()) {
            Session::remove(self::$guest); 
            return;
        } 

    }

}