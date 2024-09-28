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

    public function attempt($emailOrUsername, $password) {

        $user = Server::where('email', $emailOrUsername)
                    ->orWhere('username', $emailOrUsername)->first();

        if (!$user) {
            return false;
        }

        if ($user->password == $password) {
            $_SESSION['server'] = $user->id;
            return true;
        }

        return false;
    }

    public function logout() {
        unset($_SESSION['server']);
    }

}