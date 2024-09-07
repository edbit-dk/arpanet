<?php

namespace App\Controllers;

use App\Services\Controller;

use App\Models\User;

class CmdController extends Controller
{

    public function index($request, $response) 
    {

        return $this->view->render($response, 'terminal.twig');
    }

    public function store($request, $response) 
    {
        
    }
}