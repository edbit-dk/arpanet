<?php

namespace App\Services;

use App\Models\Host;
use App\Models\User;

class HostService {

    private $host = 'host';
    private $guest = 'guest';
    private $max_attempts = 4; // Maximum number of allowed login attempts

    public function server() 
    {
        if($this->guest()) {
            return Host::find($_SESSION[$this->guest]);
        }

        if($this->auth()) {
            return Host::find($_SESSION[$this->host]);
        }

    }

    public function admin() 
    {
        return $this->server()->username;
    }

    public function auth()
    {
        return isset($_SESSION[$this->host]);
    }

    public function guest()
    {
        return isset($_SESSION[$this->guest]);
    }

    public function connect($data)
    {
        $server = Host::where('id', $data)
        ->orWhere('ip', $data)
        ->orWhere('name', $data)
        ->where('active', 1)
        ->first();

        if (!$server) {
            return false;
        } else {
           $_SESSION[$this->guest] = $server->id;
           return true;
        }

    }

    public function logon($username, $password) {

        $server = false;
        $server_id = $this->server()->id;

        $user = auth()->login($username, $password);

        if($user) {
            $server = auth()->user()->host($server_id);
        }

        if($server) {
            session()->set($this->host, $server_id);
            return true;
        }

        $server = Host::where('id', $server_id)
            ->where('username', $username)
            ->where('password', $password)
            ->first();

        if (!$server) {
            return false;
        } else {
            session()->set($this->host, $server_id);
            return true;
        }
    }

    public function debug($pass = false) 
    {
        if($pass) {
            return $_SESSION['debug_pass'] = $pass;
        }

        if(isset($_SESSION['debug_pass'])) {
            return $_SESSION['debug_pass'];
        }

        return false;

    }

    public function attempts($attempt = false)
    {
        if (!isset($_SESSION['logon_attempts'])) {
            $_SESSION['logon_attempts'] = $this->max_attempts;
        }

        if($attempt) {
            $_SESSION['logon_attempts']--;
        }

        return $_SESSION['logon_attempts'];
    }

    public function reset()
    {
        unset($_SESSION['logon_attempts']);
        unset($_SESSION['user_blocked']);
    }

    public function blocked($block = false)
    {
        if (!isset($_SESSION['user_blocked'])) {
            $_SESSION['user_blocked'] = false;
        }

        if ($_SESSION['user_blocked'] === true) {
            return "ERROR: Terminal Locked. Please contact an administrator!";
        }

        if($block) {
            $_SESSION['user_blocked'] = true;
        }
    }

    public function logout() {
        unset($_SESSION[$this->host]);
        
    }

}