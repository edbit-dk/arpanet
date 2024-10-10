<?php

namespace App\Controllers;

use App\Services\Controller;

class DefaultController extends Controller
{

    public function index()
    {
        return view('app.php');
    }

}