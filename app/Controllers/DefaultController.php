<?php

namespace App\Controllers;

use App\Controllers\Controller;

use App\Models\User;

class DefaultController extends Controller
{

    public function index($request, $response) 
    {

        return $this->view->render($response, 'terminal.twig');
    }

}