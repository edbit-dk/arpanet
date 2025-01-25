<?php

namespace App\User;

use Lib\Controller;

use App\User\UserModel as User;
use App\User\UserService as Auth;
use App\Host\HostService as Host;

class UserController extends Controller
{

    private $user_name = 'user_name';
    private $password = 'password';
    private $firstname = 'firstname';
    private $lastname = 'lastname';
    private $created = 'created_at';
    private $access_code = 'access_code';

    private function validate($input) 
    {

        if (isset($input[0])) {
            session()->set($this->user_name, $input[0]);
        } 

        if(empty($input[1])) {
            $password = '';
        } else {
            $password = $input[1];
        }

        session()->set($this->password, $password);

    }

    public function login() 
    {
        // Check if the user is already blocked
        Auth::blocked();
        
        $data = parse_request('data');

        if(!Auth::check()) {

            $this->validate($data);

            if(session()->has($this->user_name) && session()->has($this->password)){

                $user_name = session()->get($this->user_name);
                $password = session()->get($this->password);

                $this->reset();

                if(Auth::login($user_name, $password)) {
                    Host::attempt(0);
                    echo "Authentication Successful.\n";
                    echo 'Please wait while system is accessed...';
                    sleep(1);
                    exit;         
                } else {
                    echo 'ERROR: Wrong Username.';
                    exit;
                }
            }
        }
    }

    public function user() 
    {
        $user = auth();

        echo "ACCESS CODE: {$user->access_code} \n";
        echo "SIGNUP: {$user->created_at} \n";
        echo "USERNAME: {$user->user_name} \n";
        echo "PASSWORD: {$user->password} \n";
        echo "FIRSTNAME: {$user->firstname} \n";
        echo "LASTNAME: {$user->lastname} \n";
        echo "LEVEL: {$user->level_id} \n";
        echo "XP: {$user->xp} \n";
        echo "REP: {$user->rep} \n";
    }

    public function password()
    {
        $input = parse_request('data');

        if(empty($data)) {
            echo 'ERROR: Missing Input.';
            exit;
        }

        auth()->update([
            'password' => $input[0]
        ]);

        echo 'Password Updated.';
        exit;
    }

    public function newuser() 
    {
        // Check if the user is already blocked
        Auth::blocked();

        $data = parse_request('data');

        if(empty($data)) {
            echo 'ERROR: Wrong Username.';
            exit;
        }

        $this->validate($data);
        
        if(session()->has($this->password) && session()->has($this->access_code))  {
            $access_code = session()->get($this->access_code);
            $user_name = session()->get($this->user_name);
            $password = session()->get($this->password);
            
            $this->reset();

            $firstname = ucfirst(strtolower(wordlist(text('namelist.txt'), rand(5, 12) , 1)[0]));
            $lastname = ucfirst(strtolower(wordlist(text('namelist.txt'), rand(5, 12) , 1)[0]));
        } else {
            echo 'ERROR: Wrong Input.';
            exit;
        }

        if (User::where($this->user_name, '=', $user_name)->exists()) {
            echo 'ERROR: Username Taken.';
            exit;
         }

        User::create([
            $this->user_name => $user_name,
            $this->password => $password,
            $this->access_code => $access_code,
            $this->firstname => $firstname,
            $this->lastname => $lastname,
            $this->created => \Carbon\Carbon::now()
        ]);

        sleep(1);

        Auth::login($user_name, $password);
        
        echo "Authentication Accepted.\n";
        echo 'Please wait while system is accessed...';
    }

    public function logout() 
    {
        Auth::logout();
        sleep(1);
        echo "Goodbye.\n";
    }

    public function unlink()
    {
        Auth::uplink(false);
        echo 'Disconnecting...';
    }

    public function reset()
    {
        unset($_SESSION[$this->user_name]);
        unset($_SESSION[$this->password]);
    }
}