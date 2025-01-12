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
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/events.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/helpers.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/input.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/commands.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/prompts.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/terminal.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/live/music.js');

        $css = file_get_contents(BASE_PATH . '/resources/css/reset.css');
        $css .= file_get_contents(BASE_PATH . '/resources/css/main.css');
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
        text('version.txt');
    }

    public function uplink() 
    {
        $data = parse_request('data');

        if(empty($data[0]) && !Session::has('uplink')) {
            User::blocked(false);
            return $this->accesscode();
        }

        // Initialize login attempts if not set
        Host::attempts();

        // Check if the user is already blocked
        User::blocked();

        if(Session::get('access_code') == $data[0]) {
            sleep(1);
            Session::set('uplink', true);
            Session::remove('access_code');

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

                User::blocked(true);
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
    
    public function boot() 
    {
        echo bootup();
        text('boot.txt');
    }

    public function accesscode() 
    {
        $code_1 = random_str(6, 'AXYZ01234679');
        $code_2 = random_str(6, 'AXYZ01234679');
        $code_3 = random_str(6, 'AXYZ01234679');
        $code_4 = random_str(6, 'AXYZ01234679');
    
        $access_code = "{$code_1}-{$code_2}-{$code_3}-{$code_4}"; 

        Session::set('access_code', $access_code);

        echo <<< EOT
        Welcome to TELETERM

        Uplink with central ARPANET initiated.
        Enter Security Access Code Sequence: 
        
        {$access_code}
        EOT;
    }

    public function connection()
    {
        if(Host::auth()) {
            $hostname = Host::hostname(); 
            $username = User::username();
            echo "$username@$hostname$>";
            exit;
        }

        if(Host::guest()) {
            $hostname = Host::hostname(); 
            echo "$hostname$>";
            exit;
        }

        if(User::auth()) {
            echo '@>';
        } else {
            echo '.>';
        }

    }

    public function welcome() 
    {
        if(Host::auth()) {
            return $this->host();
        }

        if(Host::guest()) {
            return $this->termlink();
        }

        if(User::auth()) {
            return $this->home();
        }

        if(Session::has('uplink')) {
            return $this->login();
        }

        return $this->accesscode();

    }

    public function home()
    {
        $last_login = date(config('date'), strtotime(User::data()->last_login));
        $username = strtoupper(User::username());
        echo <<< EOT
        Last login: {$last_login} as $username
        4.3 BSD UNIX 1986 (ARPANET) (0.0.0.0)

        Welcome to ARPANET
        EOT;
    }

    public function login()
    {
        $port = $_SERVER['SERVER_PORT'];
        $date = date('H:i l, F j, Y', time());
        $users = User::count();
        $hosts = Host::count();

        echo <<< EOT
        Connected to ARPANET port {$port}

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
        $host = Host::data();    
        $host_name = strtoupper($host->host_name);
        $host_ip = $host->ip;
        $org = $host->org;
        
        echo <<< EOT
        4.3 BSD UNIX 1986 ($host_name) ($host_ip)
        $org
        EOT;

    }

    public function host() 
    {
        $host = Host::data();
        $host_name = strtoupper($host->host_name);
        $host_ip = $host->ip;
        $org = $host->org;

        $last_login = date(config('date'), strtotime(User::data()->last_login));
        $username = strtoupper(User::username());

        echo <<< EOT
        Last login: {$last_login} as $username
        4.3 BSD UNIX 1986 ($host_name) ($host_ip)
        $org

        Welcome $username!
        EOT;

        return;

    }

}