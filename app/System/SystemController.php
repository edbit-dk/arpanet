<?php

namespace App\System;

use Lib\Controller;
use Lib\Session;

use App\User\UserService as User;
use App\Host\HostService as Host;

class SystemController extends Controller
{

    public function index()
    {
        view('app.php');
    }

    public function minify()
    {

        $js = file_get_contents(BASE_PATH . '/resources/js/live/main.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/commands.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/prompts.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/terminal.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/helpers.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/music.js');

        $css = file_get_contents(BASE_PATH . '/resources/css/main.css');
        $css .= file_get_contents(BASE_PATH . '/resources/css/bootstrap.css');
        $css .= file_get_contents(BASE_PATH . '/resources/css/terminal.css');

        file_put_contents(BASE_PATH . '/public/js/app.js', $js);
        file_put_contents(BASE_PATH . '/public/js/app.min.js', minify_js($js));

        file_put_contents(BASE_PATH . '/public/css/app.css', $css);
        file_put_contents(BASE_PATH . '/public/css/app.min.css', minify_css($css));

        print_r(file_get_contents(BASE_PATH . '/public/js/app.min.js'));
        print_r(file_get_contents(BASE_PATH . '/public/css/app.min.css'));
    }

    public function version() 
    {
        view('terminal/version.txt');
    }

    public function uplink() 
    {
        $data = parse_request('data');

        // Initialize login attempts if not set
        Host::attempts();

        // Check if the user is already blocked
        Host::blocked();

        if(Session::get('access_code') == $data[0]) {
            sleep(1);
            Session::set('uplink', true);

            echo <<< EOT
            Security Access Code Sequence Accepted.
            EOT;
            exit;

        } else {

            // Calculate remaining attempts
            $attempts_left = Host::attempts(true);
    
            if ($attempts_left == 1) {
                echo "!!! WARNING: LOCKOUT IMMINENT !!!\n\n";
            }

            // Block the user after 4 failed attempts
            if ($attempts_left == 0) {

               Host::blocked(true);
               exit;

            } else {
                echo <<< EOT
                ERROR: Incorrect Security Access Code.

                Please enter correct access code.
                Attempts left: {$attempts_left}
                Internal Security Procedures Activated.
                EOT;
            }
            
        }
    }

    public function reboot() 
    {
        echo bootup();
        view('terminal/boot.txt');
    } 
    
    public function boot() 
    {
        echo bootup();
        view('terminal/boot.txt');
    }

    public function welcome() 
    {
        if(Host::auth()) {
            return $this->host();
        }

        if(Host::guest()) {
            return $this->termlink();
        }

        if(Session::get('uplink')) {
            return $this->login();
        }

        $code_1 = random_str(6, 'AXYZ01234679');
        $code_2 = random_str(6, 'AXYZ01234679');
        $code_3 = random_str(6, 'AXYZ01234679');
        $code_4 = random_str(6, 'AXYZ01234679');
    
        $access_code = "{$code_1}-{$code_2}-{$code_3}-{$code_4}"; 

        Session::set('access_code', $access_code);
    
        echo <<< EOT
        Welcome to TELETERM.

        Uplink to central ARPANET initiated...
        
        Enter Security Access Code Sequence: 
        {$access_code}
        EOT;
    }

    public function login()
    {
        $port = $_SERVER['SERVER_PORT'];
        $date = date('H:i l, F j, Y', time());
        $users = User::count();
        $hosts = Host::count();

        echo <<< EOT
        Connected to TELETERM port {$port}

        Local time is {$date}.
        There are {$users} local users. There are {$hosts} hosts on the network.
    
        More commands available after LOGIN. 
        Type HELP for detailed command list.
        Type NEWUSER to create an account. 
        Type EXIT/LOGOUT to interrupt connection.
        EOT;
    }

    public function termlink() 
    {
        //view('terminal/auth.txt');
            
        $host_name = strtoupper(Host::hostname());
        $host_ip = Host::data()->ip;
        $level = Host::data()->level->id;

        echo <<< EOT
        -TCP/IP $host_ip-

        Connected to $host_name [LEVEL $level]

        login:
        EOT;

    }

    public function host() 
    {
        $host = Host::data();
        $host_name = strtoupper($host->host_name);
        $org = $host->org;

        $username = strtoupper(User::data()->user_name);

        echo <<< EOT
        -$host_name: $org-

        Welcome, $username
        EOT;

        return;

    }

}