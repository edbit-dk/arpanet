<?php

namespace App\System;

use Lib\Session;

use App\Host\HostModel as Hosts;

use App\User\UserService as User;
use App\Host\HostService as Host;
use App\Email\EmailService as Mail;

class SystemService 
{
    public static function uplink($input = '')
    {
        $code = User::field('code');

        if(empty($input) && !Session::has($code)) {
            User::blocked(false);
            return self::code();
        }

        // Initialize login attempts if not set
        Host::attempts();

        // Check if the user is already blocked
        User::blocked();

        if(Session::get($code) == $input) {
            sleep(1);
            User::uplink(true);
            Session::remove($code);

            echo <<< EOT
            Security Access Code Sequence Accepted.
            Accessing Mainframe...
            EOT;
            exit;

        } else {

            // Calculate remaining attempts
            $attempts_left = Host::attempts(true);
    
            if ($attempts_left == 1) {
                echo "!!! LOCKOUT IMMINENT !!!\n";
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
        $code = User::field('code');
        $access_code = access_code();

        Session::set($code, $access_code);

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

    public static function level($host_level)
    {
        if($host_level == 1) {

        }

    }

    public static function user()
    {   
        $last_login = timestamp(User::data()->last_login);
        $last_ip = User::data()->ip;

        $host = Hosts::where('id', 1)->first();
        $os = $host->os;
        $ip = $host->ip;
        $welcome = $host->welcome;
        $org = $host->org;
        $location = $host->location;

        $motd = $host->motd;
        $notes = $host->notes;
        $mail = Mail::unread();

        $system_info = isset($welcome) ? "$welcome\n" : null;
        $system_info .= isset($motd) ? "$motd\n" : null;
        $system_info .= isset($notes) ? "$notes\n" : null;
        $system_info .= isset($mail) ? "$mail\n" : null;

        echo <<< EOT
        Last login: {$last_login} from $last_ip
        Welcome to $org, $location ($os)
        
        $system_info
        EOT;
    }

    public static function connect()
    {
        $host = Host::data();
        $os = $host->os;    
        $hostname = strtoupper($host->hostname);
        $host_ip = $host->ip;
        $org = $host->org;
        
        echo <<< EOT
        $os ($hostname) ($host_ip)
        $org
        EOT;
    }

    public static function auth()
    {
        $host = Host::data();
        $os = $host->os;
        $hostname = strtoupper($host->hostname);
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
        $os ($hostname) ($host_ip)
        $org

        $motd
        $notes
        $mail       
        EOT;
    }

}
