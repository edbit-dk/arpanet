<?php

namespace App\Services;

use App\Models\Host;
use App\Models\User;

class HostService {

    private $remote_server = 'remote_server';
    private $local_server = 'local_server';
    private $max_attempts = 4; // Maximum number of allowed login attempts

    public function server() {
            return Host::find($_SESSION[$this->remote_server])->first();
    }

    public function check() {
        if($this->local() || $this->remote()) {
            return true;
        } else {
            return false;
        }
    }

    public function admin() {
        return isset($_SESSION[$this->remote_server]);
    }

    public function connect($data)
    {
        $server = Host::where('id', $data)
        ->orWhere('ip', $data)
        ->orWhere('name', $data)
        ->where('status', 1)
        ->first();

        if (!$server) {
            return false;
        } else {
           $_SESSION[$this->local_server] = $server->id;
           return true;
        }
    }

    public function local()
    {
        if(isset($_SESSION[$this->local_server])) {
            return $_SESSION[$this->local_server];
        }

        return false;
    }

    public function remote()
    {
        if(isset($_SESSION[$this->remote_server])) {
            return $_SESSION[$this->remote_server];
        }

        return false;
    }

    public function attempt($username, $password) {

        $server = false;
        $server_id = $this->local();
        $debug_pass = $this->debug();

        $user = User::where('username', $username)->first();

        if($user) {
            $server = Host::where('id', $server_id)
            ->where('user_id', $user->id)
            ->orWhere('password', $password)
            ->orWhere('password', $debug_pass)
            ->first();
        }


        if (!$server) {
            return false;
        } else {
            session()->set($this->remote_server, $server->id);
            return true;
        }
    }

    public function debug($pass = false) 
    {
        if(!isset($_SESSION['debug_pass'])) {
            return $_SESSION['debug_pass'] = false;
        }

        if($pass) {
            return $_SESSION['debug_pass'] = $pass;
        }

        return $_SESSION['debug_pass'];
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
        unset($_SESSION[$this->remote_server]);
        unset($_SESSION[$this->local_server]);
    }

}