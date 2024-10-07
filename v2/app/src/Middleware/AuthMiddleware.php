<?php

namespace App\Middleware;

use App\Services\Middleware;

class AuthMiddleware extends Middleware 
{
    public function __invoke($req, $res, $next) 
    {
        if(!$this->auth->check()) {
            return false;
        }

        return $next($req, $res);
    }
}