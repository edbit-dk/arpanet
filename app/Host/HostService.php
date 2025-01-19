<?php

namespace App\Host;

use App\Host\HostModel as Host;
use App\User\UserModel as User;
use App\User\UserService as Auth;
use App\User\Level\LevelModel as Level;
use App\System\Email\EmailModel as Email;
use App\System\Email\EmailService as Mail;

use Lib\Session;

class HostService 
{

    private static $auth = 'host_auth';
    private static $guest = 'host_guest';
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

    public static function id()
    {
        return self::data()->id;
    }

    public static function hostname()
    {
        if(self::data()) {
            return self::data()->host_name;
        } else {
            return '';
        }
    }

    public static function random($limit = 5) 
    {
        return Host::inRandomOrder()->limit($limit)->get();
    }

    public static function netstat() 
    {
        return Host::where('id','<=',4)->with('users')->get();
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

    public static function try($data)
    {
        $host = Host::where('id', $data)
        ->orWhere('ip', $data)
        ->orWhere('host_name', $data)
        ->where('active', 1)
        ->first();

        if (empty($host)) {
            return false;
        } else {
            return $host;
        }

    }

    public static function connect($data)
    {
        $host = self::try($data);

        if (empty($host)) {
            return false;
        } else {
            self::reset();
            Session::set(self::$auth, false);
            Session::set(self::$guest, $host->id);
            return true;
        }

    }

    public static function logon($username, $password = '') {

        $host = false;
        $user = false;
        $host_id = self::data()->id;

        if(empty($password)) {
            $password = null;
        }

        $host = Host::where('id',  $host_id)
            ->where('user_id', Session::get('user'))
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
            
            Session::set('session', Session::get('user'));
            Session::set('user', $user->id);
        }

         if(!Session::has('network')) {
            Session::set('network', self::auth());
        }

        self::attempt($host_id);

        return true;
    }

    public static function attempt($host_id)
    {
        if(isset($host_id)) {
            Session::set(self::$guest, false);
            Session::set(self::$auth, $host_id);

            if($host_id != 0) {
                $host_user = self::data()->user(Auth::id());
                if($host_user && empty($host_user->pivot->last_session)) {
                    $host_user->update(['last_login' => \Carbon\Carbon::now()]);
                }
            }

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
            Session::set('logon_attempts', --$attempts);
        }

        return Session::get('logon_attempts');
    }

    public static function reset()
    {
        Session::remove('logon_attempts');
        Session::remove('user_blocked');
        Session::remove('root_pass');
        Session::remove('debug_attempts');
        Session::remove('dump');
        Session::remove('root');
        Session::remove('maint'); 
    }

    public static function logoff() 
    {
        if(Session::has('session')) {
            $session = Session::get('session');
            Session::set('user', $session);
            Session::remove('session');
        }

        self::reset();


        if (self::auth()) {

            if($host_user = self::data()->user(Auth::id())) {
                $host_user->update(['last_session' => \Carbon\Carbon::now()]);
            }

            return Session::remove(self::$auth);
        }

        if(self::guest()) {
            self::attempt(session::get('network'));
            Session::remove('network');
            return Session::remove(self::$guest);
        }

    }

    public static function count()
    {
        return Host::count();
    }

    public static function root()
    {
        $host = self::hostname();
        $user = Auth::username();
        $contact = Mail::contact();

        $email = Email::where('sender', $contact)
        ->where('recipient', "system@$host")
        ->where('is_read', 0);

        if($email->exists()) {
            $email->update(['is_read' => 1]);

            $root_hack = "#!/bin/bash echo '$user ALL=(ALL) ALL' >> /sys/passwd";

            if(similar_text($root_hack, $email->first()->body) > 50) {
                $date = timestamp();
                $hostname = strtoupper($host);
                $username = strtoupper($user);
                $note = "Note: $username has ROOT on $hostname as of $date";
    
                self::data()->update([
                    'user_id' => Auth::id(),
                    'notes' => $note
                ]);
            } else {
                $data = [
                    0=> "send ERROR $contact", 
                    1=> "SYSTEM ERROR: Unknown Command. Please use valid system commands!"
                ];
                Mail::send($data, "system@$host");
            }

        }
    }

}