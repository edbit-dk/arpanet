<?php

namespace App\System;

use Lib\Controller;
use Lib\Session;

use App\User\UserService as User;

use App\Host\HostService as Host;
use App\Host\HostModel as Hosts;

use App\Host\Folder\FolderService as Folder;
use App\System\Email\EmailService as Mail;

class SystemController extends Controller
{

    public function index()
    {
        Session::set('term', 'RIT-V300');
        view('app.php');
    }

    public function mode()
    {
        $data = parse_request('data');
        Session::set('term', strtoupper($data[0]));
    }

    public function minify()
    {

        $js = file_get_contents(BASE_PATH . '/resources/js/main.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/events.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/helpers.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/input.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/commands.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/prompts.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/terminal.js');
        $js .= file_get_contents(BASE_PATH . '/resources/js/music.js');

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
        echo text('version.txt');
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
            User::uplink(true);
            Session::remove('access_code');

            echo <<< EOT
            Security Access Code Sequence Accepted.
            Accessing Mainframe...
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
        echo bootup() . "\n\n";
        echo text('boot.txt');
    }

    public function accesscode() 
    {    
        $access_code = access_code();

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
        $pwd = Folder::pwd();

        if(Host::guest()) {
            $hostname = Host::hostname(); 
            echo "[@$hostname]";
            exit;
        }
        
        if(Host::auth()) {
            $hostname = Host::hostname(); 
            $username = User::username();
            if(Host::data()->user_id == User::id()) {
                echo "[$username@$hostname$pwd]#";
            } else {  
                echo "[$username@$hostname$pwd]$";
            }
            exit;
        }

        echo '.';

    }

    public function main() 
    {
        if(Host::guest()) {
            return $this->termlink();
        }

        if(Host::auth() == 1) {
            return $this->home();
        }

        if(Host::auth() > 1) {
            return $this->host();
        }

        if(User::isUplinked()) {
            return $this->login();
        }

        return $this->accesscode();

    }

    public function home()
    {
        $host = Hosts::where('host_name', 'arpanet')->first();
        $os = $host->os;
        $ip = $host->ip;
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
        
        $os ($hostname/$ip)
        $org

        $system_info
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

        ARPANET Login System
        Authorized users only.

        login:
        EOT;
    }

    public function termlink() 
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

    public function host() 
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

        return;

    }

}