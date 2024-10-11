<?php

namespace App\Controllers;

use App\Services\Controller;

class DefaultController extends Controller
{

    public function index($app)
    {
        return view('app.php');
    }

}