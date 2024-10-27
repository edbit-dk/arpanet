<?php

namespace App\Services;

use App\Providers\App;
use App\Services\UserService;

class AuthService
{
    public static function check() 
    {
        var_dump((new UserService)->check());
    }
}