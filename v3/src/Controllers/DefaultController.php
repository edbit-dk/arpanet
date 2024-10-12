<?php

namespace App\Controllers;

use App\Providers\Controller;

class DefaultController extends Controller
{

    public function index()
    {
        view('app.php');
    }

}