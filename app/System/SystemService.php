<?php

namespace App\System;

use Lib\Session;

use App\Host\HostModel as Hosts;

use App\User\UserService as User;
use App\Host\HostService as Host;
use App\Email\EmailService as Mail;

class SystemService 
{

    private static $uplink = 'uplink';

    public static function uplink($input = '')
    {
        $code = User::field('code');

        if(empty($input) && !Session::has(self::$uplink)) {
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

            echo <<< EOT
            Security Access Code Sequence Accepted.
            Accessing Mainframe...
            EOT;
            exit;

        } else {

            // Calculate remaining attempts
            $attempts_left = Host::attempts(true);
    
            if ($attempts_left == 1) {
                echo "--LOCKOUT IMMINENT--\n\n";
            }

            // Block the user after 4 failed attempts
            if ($attempts_left == 0) {

                User::blocked(true);
                exit;

            } else {
                echo <<< EOT
                *** ACCESS DENIED ***
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
        WELCOME TO TELETERM 1.0
        
        Uplink with central ARPANET initiated.
        Enter Security Access Code Sequence: 
        
        {$access_code}
        EOT;
    }

    public static function login()
    {
        sleep(1);

        $port = $_SERVER['SERVER_PORT'];

        echo <<< EOT
        Connected to ARPANET port {$port}

        ARPANET LOGIN SYSTEM
        Authorized users only.
        EOT;
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
        $system_info .= isset($notes) ? "\n$notes" : null;
        $system_info .= isset($mail) ? "\n$mail" : null;

        $current_date = date('H:i:s l, F j', $host->created_at);

        echo <<< EOT
        Last login: {$last_login} from $last_ip
        ($os): $current_date

        Welcome to $org, $location
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
        $hostname ($host_ip)
        $org
        EOT;
    }

    public static function auth()
    {
        $host = Host::data();
        $last_ip = User::data()->ip;
        $os = $host->os;
        $welcome = $host->welcome;
        $location = $host->location;
        $motd = isset($host->motd) ? $host->motd : null;
        $notes = isset($host->notes) ? $host->notes : null;
        $org = $host->org;
        $username = strtoupper(User::username());
        $last_login = '';

        if($host_user = Host::data()->user(User::id())) {

            if(empty($host_user->pivot->last_session)) {
              $host_user->pivot->last_session = now();
              $host_user->pivot->save();
            }
            $date = timestamp($host_user->pivot->last_session);
            $last_login = "$date as $username";
        }

        
        $current_date = date('H:i:s l, F j, Y', $host->created_at);

        $emails = Mail::unread();
        $mail = $emails;

        $system_info = isset($welcome) ? "$welcome\n" : null;
        $system_info .= isset($motd) ? "$motd\n" : null;
        $system_info .= isset($notes) ? "\n$notes" : null;
        $system_info .= isset($mail) ? "\n$mail" : null;

        Host::root();

        echo <<< EOT
        Last login: {$last_login} from $last_ip
        ($os): $current_date

        Welcome to $org, $location
        $system_info 
        EOT;
    }

}
