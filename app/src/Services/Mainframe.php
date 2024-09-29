<?php

namespace App\Services;

use App\Models\Server;

class Mainframe {

    public function server() {
        return Server::find($_SESSION['server']);
    }

    public function check() {
        return isset($_SESSION['server']);
    }

    public function connect($data)
    {
        $server = Server::where('id', $data)
        ->orWhere('name', $data)
        ->where('status', 1)
        ->first();

        if (!$server) {
            return false;
        } else {
            return true;
        }
    }

    public function attempt($data, $password) {

        $server = Server::where('id', $data)
        ->orWhere('name', $data)
        ->where('status', 1)
        ->first();

        if (!$server) {
            return false;
        }

        if ($server->admin_pass == $password) {
            $_SESSION['server'] = $server->id;
            return true;
        }

        return false;
    }

    public function logout() {
        unset($_SESSION['server']);
    }

}