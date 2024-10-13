<?php

namespace App\Controllers;

use App\Models\User;
use App\Providers\Controller;

class AuthController extends Controller
{

    private $email = 'email';
    private $password = 'password';
    private $firstname = 'firstname';
    private $lastname = 'lastname';
    private $created = 'created_at';
    private $access_code = 'access_code';

    private function validate($data) {

        $input = explode(' ', trim($data));

        if (count($input) >= 2) {

            $user[$this->email] = $input[0];
            $user[$this->password] = $input[1];

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
            echo 'ERROR: Missing parameters.';
            exit;
        } else {
            $email = $user[$this->email];
            $password = $user[$this->password];
        }

        sleep(1);

        if($this->auth->attempt($email, $password)) {
            echo "Security Access Code Sequence Accepted.\n"; 
            echo "Welcome to PoseidoNet!\n";
            exit;         
        } else {
            echo 'ERROR: Wrong credentials!';
            exit;
        }

    }

    public function register() 
    {
        $data = request()->get('data');

        if(empty($data)) {
            echo 'ERROR: Missing parameters.';
            exit;
        }

        $user = $this->validate($data);

        if(!$user) {
            echo 'ERROR: Missing parameters.';
            exit;
        } else {
            $password = $user[$this->password];
            $email = $user[$this->email];

            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email_username = explode('@', $email)[0];
            } else {
                $email = '';
                $email_username = $email;
            }

            $firstname = ucfirst(strtolower(wordlist($this->config['views'] . '/lists/namelist.txt', rand(5, 12) , 1)[0]));
            $lastname = ucfirst(strtolower(wordlist($this->config['views']. '/lists/namelist.txt', rand(5, 12) , 1)[0]));
        }

        if (User::where($this->email, '=', $email)->exists()) {
            echo 'ERROR: User taken!';
            exit;
         }

        $user_id = User::insertGetId([
            $this->password => $password,
            $this->email => $email,
            $this->access_code => session()->find('access_code'),
            $this->firstname => $firstname,
            $this->lastname => $lastname,
            $this->created => \Carbon\Carbon::now()
        ]);

        if(empty($user_id)) {
            echo 'ERROR: Wrong credentials!';
            exit;
        }

        $username = 'PE-' . strtoupper(random_username($email_username, $user_id));

        $user = User::find($user_id);
        $user->username = $username;
        $user->save();

        sleep(1);

        $this->auth->attempt($username, $password);

        echo "Security Access Code Accepted.\n";
        echo "Welcome to PoseidoNET!\n";
        exit;
    }

    public function logout() {

        auth()->logout();
    
        echo "DISCONNECTING from PoseidoNET...\n";
    }
}