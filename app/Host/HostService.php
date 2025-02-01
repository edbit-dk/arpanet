<?php

namespace App\Host;

use App\Host\HostModel as Host;
use App\User\UserModel as User;
use App\User\UserService as Auth;
use App\Level\LevelModel as Level;
use App\Email\EmailModel as Email;
use App\Email\EmailService as Mail;

use Lib\Session;

class HostService 
{

    private static $auth = 'host_auth';
    private static $guest = 'host_guest';
    private static $user = 'host_user';
    private static $session = 'host_session';
    private static $sessions = [];
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
        if(self::data()) {
            return self::data()->id;
        }

        return false;
        
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
        return Host::where('network', 1)->get();
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
            if(self::auth() == 1) {
               
            }
            Session::set(self::$guest, $host->id);
            self::session(true,$host->id, Auth::id());
            return true;
        }

    }

    public static function rlogin($data)
    {
        $user_id = Auth::id();

        if(isset($data[1])) {
            if($user = User::where('user_name', $data[1])->first()) {
                $user_id = $user->id;
            }
        }

        if($host = self::try($data[0])) {
            if($host->user($user_id)) {
                self::session(true, $host->id, $user_id);
                self::attempt($host->id);
                return true;
            }
        }

        return false;
    }

    public static function logon($username, $password = '') {

        $host = false;
        $user = false;
        $host_id = self::data()->id;
        $user_id = Auth::id();

        if(empty($password)) {
            $password = null;
        }

        $host = Host::where('id',  $host_id)
            ->where('user_id', Auth::check())
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

            $user_id = $user->id;
            Auth::attempt($user_id);
        }

        self::session(true, $host_id, $user_id);
        self::attempt($host_id);

        return true;
    }

    public static function session($new = true, $host_id = '', $user_id = '')
    {
        if(!Session::has(self::$session)) {
            Session::set(self::$session, []);
        }

        if(!$new) {
            self::$sessions = Session::get(self::$session);
            array_pop(self::$sessions); 
            Session::set(self::$session, self::$sessions);
            $last_session = end(self::$sessions);
            if($last_session) {
                return $last_session;
            }
        }

        if($new) {
            self::$sessions = Session::get(self::$session);
            if(!array_has(self::$sessions, self::$auth, $host_id)) {
                self::$sessions[] = [self::$auth => $host_id, self::$user => $user_id];
                Session::set(self::$session, self::$sessions);
            }
            return Session::get(self::$session);
        }

        return false;
    }

    public static function attempt($host_id, $user_id = '')
    {
        if(is_int($host_id)) {
            Session::set(self::$guest, false);
            Session::set(self::$auth, $host_id);

            if($host_id != 1) {
                $host_user = self::data()->user(Auth::id());
                if($host_user && empty($host_user->pivot->last_session)) {
                    $host_user->pivot->last_session = \Carbon\Carbon::now();
                    $host_user->pivot->save();
                }
            }

            return self::data();
        }
    }

    public static function debug($pass, $user) 
    {
        $host_id = self::data()->id;

        if($user = self::data()->user($user)) {
            self::attempt($host_id, $user);
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
        self::reset();

        if (self::auth() > 1) {

            if($host_user = self::data()->user(Auth::id())) {
                $host_user->pivot->last_session = \Carbon\Carbon::now();
                $host_user->pivot->save();
            }
        }

        self::$sessions = self::session(false);
        Session::remove(self::$guest);
        Session::remove(self::$auth);

        if(!empty(self::$sessions)) {
            Auth::attempt(self::$sessions[self::$user]);
            self::attempt(self::$sessions[self::$auth], self::$sessions[self::$user]);
        } else {
            Session::remove(self::$auth);
            Auth::logout();
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
        ->where('subject', 'cron')
        ->where('recipient', "root@$host")
        ->where('is_read', 0);

        if($email->exists()) {
            $email->update(['is_read' => 1]);

            $root_hack = "#!/bin echo '$user ALL=(ALL) ALL' >> /sys/passwd";

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
                    1=> "ERROR: Unknown Command."
                ];
                Mail::send($data, $contact);
            }

        }
    }

}