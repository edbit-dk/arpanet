<?php

namespace App\System;

use Lib\Session;

use App\Host\HostModel as Hosts;

use App\User\UserService as User;
use App\Host\HostService as Host;
use App\Email\EmailService as Mail;

class SystemService 
{
    public static $code = 'code';
    public static $uplink = 'uplink';

    public static function uplink($input = '')
    {
        if(empty($input) && !Session::has(self::$uplink)) {
            User::blocked(false);
            return self::code();
        }

        // Initialize login attempts if not set
        Host::attempts();

        // Check if the user is already blocked
        User::blocked();

        if(Session::get(self::$code) == $input) {
            sleep(1);
            User::uplink(true);
            Session::remove(self::$code);

            echo <<< EOT
            Security Access Code Sequence Accepted.
            Accessing Mainframe...
            EOT;
            exit;

        } else {

            // Calculate remaining attempts
            $attempts_left = Host::attempts(true);
    
            if ($attempts_left == 1) {
                echo "WARNING: LOCKOUT IMMINENT !\n";
            }

            // Block the user after 4 failed attempts
            if ($attempts_left == 0) {

                User::blocked(true);
                exit;

            } else {
                echo <<< EOT
                ERROR: Incorrect Security Access Code. 
                Internal Security Procedures Activated.
                EOT;
            }
            
        }
    }

    public static function code()
    {
        $access_code = access_code();

        Session::set(self::$code, $access_code);

        echo <<< EOT
        Welcome to TELETERM

        Uplink with central ARPANET initiated.
        Enter Security Access Code Sequence: 
        
        {$access_code}
        EOT;
    }

    public static function login()
    {
        $port = $_SERVER['SERVER_PORT'];

        echo <<< EOT
        Connected to ARPANET port {$port}

        ARPANET LOGIN SYSTEM
        Authorized users only.
        EOT;
    }

    public static function user()
    {        
        $date = date('H:i l, F j, Y', time());
        $users = User::count();
        $hosts = Host::count();

        $host = Hosts::where('host_name', 'arpanet')->first();
        $os = $host->os;
        $ip = $host->ip;
        $welcome = $host->welcome;
        $org = $host->org;
        $hostname = $host->host_name;
        $last_login = timestamp(User::data()->last_login);
        $last_ip = User::data()->ip;

        $motd = $host->motd;
        $notes = $host->notes;
        $mail = Mail::unread();

        $system_info = isset($motd) ? "$motd\n" : null;
        $system_info .= isset($notes) ? "$notes\n" : null;
        $system_info .= isset($mail) ? "$mail" : null;

        echo <<< EOT
        Last login: {$last_login} from $last_ip
        $os ($hostname, $ip)
        $org

        Local time is {$date}.
        There are {$users} local users. There are {$hosts} hosts on the network.

        $system_info
        EOT;
    }

    public static function connect()
    {
        $host = Host::data();
        $os = $host->os;    
        $host_name = strtoupper($host->host_name);
        $host_ip = $host->ip;
        $org = $host->org;
        
        echo <<< EOT
        $os ($host_name) ($host_ip)
        $org
        EOT;
    }

    public static function auth()
    {
        $host = Host::data();
        $os = $host->os;
        $host_name = strtoupper($host->host_name);
        $host_ip = $host->ip;
        $motd = isset($host->motd) ? $host->motd : null;
        $notes = isset($host->notes) ? $host->notes : null;
        $org = $host->org;
        $username = strtoupper(User::username());
        $last_login = '';

        if($host_user = Host::data()->user(User::id())) {

            if(empty($host_user->pivot->last_session)) {
              $host_user->pivot->last_session = \Carbon\Carbon::now();
              $host_user->pivot->save();
            }
            $date = timestamp($host_user->pivot->last_session);
            $last_login = "$date as $username";
        }

        $emails = Mail::unread();
        $mail = $emails;

        Host::root();

        echo <<< EOT
        Last login: {$last_login}
        $os ($host_name) ($host_ip)
        $org

        $motd
        $notes
        $mail       
        EOT;
    }

}
