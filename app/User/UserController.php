<?php

namespace App\User;

use Lib\Controller;
use Lib\Session;

use App\User\UserModel as User;

use App\User\UserService as Auth;
use App\Host\HostService as Host;
use App\System\CronService as Cron;


class UserController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = User::fields();
    }
 
    private function validate($input) 
    {
        if (isset($input[0])) {
            Session::set($this->user['username'], $input[0]);
        } 

        if(empty($input[1])) {
            $password = '';
        } else {
            $password = $input[1];
        }

        Session::set($this->user['password'], $password);

    }

    public function login() 
    {
        // Check if the user is already blocked
        Auth::blocked();
        
        $data = parse_request('data');

        if(!Auth::check()) {

            $this->validate($data);

            if(Session::has($this->user['username']) && Session::has($this->user['password'])){

                $username = Session::get($this->user['username']);
                $password = Session::get($this->user['password']);

                $this->reset();

                if(Auth::login($username, $password)) {
                    Host::attempt(1, Auth::id());
                    Cron::stats();

                   $host = Host::data();

                    $ip = $host->ip;
                    $host = $host->hostname;

                    sleep(1);

                    echo <<< EOT
                    Connecting...
                    Trying $ip
                    Connected to $host\n
                    EOT;
                    exit;         
                } else {
                    echo 'IDENTIFICATION NOT RECOGNIZED BY SYSTEM';
                    exit;
                }
            }
        }
    }

    public function user() 
    {
        $user = auth();
        $password = base64_encode($user->password);

        echo "ACCESS CODE: {$user->code} \n";
        echo "SIGNUP: {$user->created_at} \n";
        echo "USERNAME: {$user->username} \n";
        echo "PASSWORD: {$password} \n";
        echo "LEVEL: {$user->level_id} \n";
        echo "XP: {$user->xp} \n";
        echo "REP: {$user->rep} \n";
    }

    public function password()
    {
        $input = parse_request('data');

        if(empty($data)) {
            echo 'MISSING INPUT.';
            exit;
        }

        Auth::data()->update([
            'password' => $input[0]
        ]);

        echo 'PASSWORD UPDATED.';
        exit;
    }

    public function newuser() 
    {
        // Check if the user is already blocked
        Auth::blocked();

        $data = parse_request('data');

        if(empty($data)) {
            echo 'WRONG USERNAME.';
            exit;
        }

        $this->validate($data);
        
        if(Session::has($this->user['username']) && Session::has($this->user['password']))  {
            $code = Session::get($this->user['code']);
            $username = Session::get($this->user['username']);
            $password = Session::get($this->user['password']);
            
            $this->reset();
        } else {
            echo 'WRONG INPUT.';
            exit;
        }

        if (User::where($this->user['username'], '=', $username)->exists()) {
            echo 'USERNAME TAKEN.';
            exit;
         }

        User::create([
            $this->user['username'] => $username,
            $this->user['email'] => "$username@teleterm.net",
            $this->user['fullname'] => ucfirst($username),
            $this->user['password'] => $password,
            $this->user['code'] => $code,
            $this->user['created'] => now()
        ]);

        if(Auth::login($username, $password)) {
            Host::attempt(1);
            Host::session(true, 1, Auth::id());
            Cron::stats();

           $host = Host::data();

            $ip = $host->ip;
            $host = $host->hostname;

            sleep(1);
            
            echo <<< EOT
            Connecting...
            Trying $ip
            Connected to $host\n
            EOT;
            exit;          
        } else {
            echo 'IDENTIFICATION NOT RECOGNIZED BY SYSTEM';
            exit;
        }
    }

    public function logout() 
    {
        Auth::logout();
    }

    public function unlink()
    {
        Auth::uplink(false);
        echo '--DISCONNECTING--';
    }

    public function reset()
    {
        unset($_SESSION[$this->user['username']]);
        unset($_SESSION[$this->user['password']]);
    }
}