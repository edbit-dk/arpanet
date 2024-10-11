<?php

namespace App\Controllers;

use App\Services\Controller;

class RobcoController extends Controller
{

    public function index($router)
    {
        return view('app.php');
    }

}