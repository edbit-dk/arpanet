<?php

namespace App\Controllers;

use App\Providers\Controller;

use App\Models\User;

class DefaultController extends Controller
{

    public function index()
    {
        view('app.php');
    }

    public function test()
    {
        echo "EXCACT";
    }

}