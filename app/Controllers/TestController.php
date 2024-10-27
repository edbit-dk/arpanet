<?php

namespace App\Controllers;

use Custom\Controller;

use App\Services\AuthService as Auth;

class TestController extends Controller
{

    public function index()
    {
        dd(Auth::check());
    }

}