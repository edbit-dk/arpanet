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

        if (count($input) >= 2 && ctype_alnum($input[1])) {

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
            return 'ERROR: Missing parameters.';
        }

        $user = $this->validate($data);

        if(!$user) {
            return 'ERROR: Missing parameters.';
        } else {
            $email = $user[$this->email];
            $password = $user[$this->password];
        }

        sleep(1);

        if($this->auth->attempt($email, $password)) {
            echo "Security Access Code Sequence Accepted.\n"; 
            return "Welcome to PoseidoNet!\n";         
        } else {
            return 'ERROR: Wrong credentials!';
        }

    }

    public function register() 
    {
        $data = request()->get('data');

        if(empty($data)) {
            return 'ERROR: Missing parameters.';
        }

        $user = $this->validate($data);


        if(!$user) {
            return 'ERROR: Missing parameters.';
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
            return 'ERROR: User taken!';
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
            return 'ERROR: Wrong credentials!';
        }

        $username = 'PE-' . strtoupper(random_username($email_username, $user_id));

        $user = User::find($user_id);
        $user->username = $username;
        $user->save();

        sleep(1);

        $this->auth->attempt($username, $password);

        echo "Security Access Code Accepted.\n";
        return "Welcome to PoseidoNET!\n";
    }

    public function logout() {

        $this->auth->logout();
    
        return "DISCONNECTING from PoseidoNET...\n";
    }
}