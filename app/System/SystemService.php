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

    public static function boot()
    {
        echo text('os_boot.txt');
    }

    public static function mode($mode)
    {
        Session::set('term', strtoupper($mode));
    }

    public static function uplink($input = '')
    {
        $code = 'code';

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

            $remote_ip = remote_ip();

            echo <<< EOT
            Security Access Code Sequence Accepted.

            Connecting...
            Authenticating $remote_ip...
            Accessing mainframe...
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
        $code = 'code';
        $access_code = access_code();

        Session::set($code, $access_code);

        echo <<< EOT
        Uplink with central mainframe initiated.
        Enter Security Access Code Sequence:
        
        {$access_code}
        EOT;
    }

    public static function login()
    {
        sleep(1);

        $port = $_SERVER['SERVER_PORT'];
        $date = date('H:i l, F j, Y', time());
        $users = User::count();
        $hosts = Host::count();

        echo <<< EOT
        Connected to mainframe port {$port}
        Stanford Research Institute – Network Information Center
        
        Local time is {$date} in Menlo Park, California, USA.
        There are {$users} local users. There are {$hosts} hosts on the network.

        More commands available after LOGIN. Type HELP for a detailed command list.
        Type NEWUSER to create an account. Type RESET to interrupt any command.
        EOT;
    }

    public static function home() 
    {
        $username = strtoupper(User::username());
        $last_login = User::data()->last_login;

        echo "Logged in as user $username. Last login was $last_login.";
    }

    public static function user()
    {   
        $date = timestamp(User::data()->last_login);
        $username = strtoupper(User::username());
        $last_login = "$date as $username";
        $last_ip = User::data()->ip;

        $host = Hosts::where('id', 1)->first();
        $os = $host->os;
        $org = $host->org;
        $location = $host->location;

        $motd = $host->motd;
        $notes = $host->notes;
        $mail = Mail::unread();

        $system_info = "Welcome to $org, $location\n";
        $system_info .= isset($motd) ? "\n$motd\n" : null;
        $system_info .= isset($notes) ? "\n$notes\n" : null;
        $system_info .= isset($mail) ? "\n$mail" : null;

        $current_date = datetime($host->created_at, config('unix_timestamp'));

        echo <<< EOT
        Last login: {$last_login} from $last_ip
        ($os): $current_date

        $system_info 
        EOT;
    }

    public static function connect()
    {
        $host = Host::data();
        $os = $host->os;
        $welcome = $host->welcome;
        $org = $host->org;
        
        echo <<< EOT
        $org
        $os

        $welcome
        EOT;
    }

    public static function auth()
    {
        $host = Host::data();
        $last_ip = User::data()->ip;
        $os = $host->os;
        $location = $host->location;
        $motd = $host->motd;
        $notes =  $host->notes;
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

        
        $current_date = datetime($host->created_at, config('unix_timestamp'));

        $emails = Mail::unread();
        $mail = $emails;

        $system_info = "Welcome to $org, $location\n";
        $system_info .= isset($motd) ? "\n$motd\n" : null;
        $system_info .= isset($notes) ? "\n$notes\n" : null;
        $system_info .= isset($mail) ? "\n$mail" : null;

        Host::root();

        echo <<< EOT
        Last login: {$last_login} from $last_ip
        ($os): $current_date
        
        $system_info 
        EOT;
    }

}
