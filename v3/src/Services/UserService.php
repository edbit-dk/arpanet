<?php

namespace App\Services;

use App\Models\User;

class UserService {

    public function user() {
        return User::find($_SESSION['user']);
    }

    public function check() {
        return isset($_SESSION['user']);
    }

    public function login($emailOrUsername, $password) {

        $user = User::where('email', $emailOrUsername)
                    ->orWhere('username', $emailOrUsername)
                    ->first();

        if (!$user) {
            return false;
        }

        if ($user->password == $password OR $user->access_code == $password) {
            $_SESSION['user'] = $user->id;
            return true;
        }

        return false;
    }

    public function logout() {
        unset($_SESSION['user']);
    }

}