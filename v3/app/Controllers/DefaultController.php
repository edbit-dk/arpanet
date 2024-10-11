<?php

namespace App\Controllers;

use App\Services\Controller;

class DefaultController extends Controller
{

    public function index($router)
    {
        return view('app.php');
    }

}