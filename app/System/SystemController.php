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

        $js = file_get_contents(BASE_PATH . '/resources/js/main.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/commands.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/helpers.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/prompts.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/terminal.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/music.js');

        $css = file_get_contents(BASE_PATH . '/resources/css/main.css');
        $css .= file_get_contents(BASE_PATH . '/resources/css/bootstrap.css');
        $css .= file_get_contents(BASE_PATH . '/resources/css/terminal.css');

        file_put_contents(BASE_PATH . '/public/js/app.min.js', minify_js($js));
        file_put_contents(BASE_PATH . '/public/css/app.min.css', minify_css($css));

        print_r(file_get_contents(BASE_PATH . '/public/js/app.min.js'));
        print_r(file_get_contents(BASE_PATH . '/public/css/app.min.css'));
    }

    public function test()
    {
        
    }

    public function version() 
    {
        view('terminal/version.txt');
    }

    public function uplink() 
    {

    
        return;
    }

    public function enter()
    {
        $data = parse_request('data');

        // Initialize login attempts if not set
        Host::attempts();

        // Check if the user is already blocked
        Host::blocked();

        if(Session::get('access_code') == $data[0]) {
            echo <<< EOT
            Security Access Code Sequence Accepted.

            *** NETWORK STATUS: ONLINE ***

            1. Type LOGIN for authentication.
            2. Type NEWUSER to create an account.
            3. Type HELP for a command list.
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
                Please ENTER correct code.
                Attempts left: {$attempts_left}
                Internal Security Procedures Activated.
                EOT;
            }
            
        }

    }

    public function help()
    {

        $help = [];

        if(User::auth() && !Host::auth()) {
            $help = require config('path') . '/storage/array/user.php';
        }
        
        if(Host::auth()) {
            $help = require config('path') . '/storage/array/host.php';
        }

        if(Host::guest()) {
            $help = require config('path') . '/storage/array/guest.php';
        }

       if(empty($help)) {
            $help = require config('path') . '/storage/array/visitor.php';
       }
        
        $output = "HELP:\n";
        foreach ($help as $cmd => $text) {
            $output .= " $cmd $text\n";
        }
        echo $output;
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

        if(User::auth()) {
            return view('terminal/termlink.txt');
        }

        view('terminal/welcome.txt');

        $code_1 = random_str(6, 'AXYZ01234679');
        $code_2 = random_str(6, 'AXYZ01234679');
        $code_3 = random_str(6, 'AXYZ01234679');
        $code_4 = random_str(6, 'AXYZ01234679');
    
        $access_code = "{$code_1}-{$code_2}-{$code_3}-{$code_4}"; 

        Session::set('access_code', $access_code);
    
        echo <<< EOT
        *** NETWORK STATUS: OFFLINE ***
        Uplink with central ARPANET initiated.
        Enter Security Access Code Sequence:
        ***********************************
        >>> {$access_code} <<<
        ***********************************
        EOT;
    }

    public function termlink() 
    {
        view('terminal/auth.txt');
            
        $host_name = Host::hostname();
        $host_ip = Host::data()->ip;
        $level = Host::data()->level->id;

        echo <<< EOT
                   -Server $host_ip-
                      
        Connected to: $host_name
        Password Required             [SECURITY: $level]
        EOT;

    }

    public function host() 
    {
        view('terminal/auth.txt');

        $host = Host::data();
        $host_name = strtoupper($host->host_name);
        $org = $host->org;

        $username = User::data()->user_name;

        echo <<< EOT
        $host_name | $org

        Welcome, $username 
        EOT;

        return;

    }

}