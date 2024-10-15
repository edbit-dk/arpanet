<?php

namespace App\Controllers;

use App\Models\User;
use App\Providers\Controller;

class AuthController extends Controller
{

    private $username = 'username';
    private $password = 'password';
    private $firstname = 'firstname';
    private $lastname = 'lastname';
    private $created = 'created_at';
    private $access_code = 'access_code';

    private function validate($data) {

        $input = explode(' ', trim($data));

        if (count($input) >= 2) {

            $user[$this->username] = $input[0];
            $user[$this->access_code] = $input[1];

        } else {
            return false;
        }

        return $user;
    }

    public function login() 
    {
        $data = request()->get('data');

        if(empty($data)) {
            echo 'ERROR: Missing parameters.';
            exit;
        }

        $user = $this->validate($data);

        if(!$user) {
            echo 'ERROR: Input errors. Please try again!';
            exit;
        } else {
            $access_code = $user[$this->access_code];
            $username = $user[$this->username];
        }

        sleep(1);

        if($this->user->login($username, $access_code)) {
            echo "Security Access Code Sequence Accepted.\n"; 
            echo "Trying to connect...\n";
            exit;         
        } else {
            echo 'ERROR: WRONG USERNAME';
            exit;
        }

    }

    public function user() 
    {
        $user = auth()->user();

        echo "ACCESS CODE: {$user->access_code} \n";
        echo "SIGNUP: {$user->created_at} \n";
        echo "USERNAME: {$user->username} \n";
        echo "PASSWORD: {$user->password} \n";
        echo "FIRSTNAME: {$user->firstname} \n";
        echo "LASTNAME: {$user->lastname} \n";
        echo "LEVEL: {$user->level_id} \n";
        echo "XP: {$user->xp} \n";
        echo "REP: {$user->rep} \n";
    }

    public function password()
    {
        $data = request()->get('data');

        if(empty($data)) {
            echo 'ERROR: MISSING INPUT';
            exit;
        }

        $input = explode(' ', trim($data))[0];

        auth()->user()->update([
            'password' => $input
        ]);

        echo 'Password updated!';
        exit;
    }

    public function newuser() 
    {
        $data = request()->get('data');

        if(empty($data)) {
            echo 'ERROR: Missing parameters.';
            exit;
        }

        $user = $this->validate($data);

        if(!$user) {
            echo 'ERROR: Input errors. Please try again!';
            exit;
        } else {
            $access_code = $user[$this->access_code];
            $username = $user[$this->username];

            $firstname = ucfirst(strtolower(wordlist($this->config['views'] . '/lists/namelist.txt', rand(5, 12) , 1)[0]));
            $lastname = ucfirst(strtolower(wordlist($this->config['views']. '/lists/namelist.txt', rand(5, 12) , 1)[0]));
        }

        if (User::where($this->username, '=', $username)->exists()) {
            echo 'ERROR: Username taken!';
            exit;
         }

        User::create([
            $this->username => $username,
            $this->access_code => session()->get('access_code'),
            $this->firstname => $firstname,
            $this->lastname => $lastname,
            $this->created => \Carbon\Carbon::now()
        ]);

        sleep(1);

        $this->user->login($username, $access_code);
        
        echo "Security Access Code Accepted.\n";
        exit;
    }

    public function logout() {

        auth()->logout();
    
        echo "DISCONNECTING from PoseidoNET...\n";
    }
}