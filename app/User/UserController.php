<?php

namespace App\User;

use Lib\Session;
use Lib\Input;

use App\User\UserModel as User;

use App\User\UserService as Auth;
use App\Host\HostService as Host;
use App\AppService as App;

use App\AppController;

class UserController extends AppController
{
    public function login() 
    {
        // Check if the user is already blocked
        Auth::blocked();

        if(!Auth::check()) {

            $input = App::auth($this->data);

            if(Auth::login($input['username'], $input['password'])) {
                Host::attempt(1, Auth::id());
                sleep(1);
                echo 'IDENTIFICATION VERIFIED';
                exit;  

            } else {
                echo 'IDENTIFICATION NOT RECOGNIZED BY SYSTEM';
                exit;
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
        $input = $this->request;

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

        $data = $this->request;

        if(empty($data)) {
            echo 'WRONG USERNAME.';
            exit;
        }

        $input = App::auth($this->data);
        
        if(Session::has($this->user['username']) && Session::has($this->user['password']))  {
            $code = Session::get($this->user['code']);
            $username = Session::get($this->user['username']);
            $password = Session::get($this->user['password']);

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
            Host::attempt(1, Auth::id());

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
}