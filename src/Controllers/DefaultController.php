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
        $firstname = wordlist($this->config['views'] . '/lists/namelist.txt', rand(5, 12) , 1);
        $lastname = wordlist($this->config['views'] . '/lists/namelist.txt', rand(5, 12) , 1);

        var_dump($firstname, $lastname);
    }

}