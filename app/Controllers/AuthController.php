<?php

namespace App\Controllers;

use App\Controllers\Controller;

use App\Models\User;

class AuthController extends Controller
{

    public function index($request, $response) 
    {

        return $this->view->render($response, 'terminal.twig');
    }

    public function register($request, $response) 
    {
        $data = $request->getParams();

        var_dump($data);
        die;

        if(empty($data)) {
            return 'ERROR: Missing parameters.';
        }

        return $response->withRedirect($this->router->pathFor('default'));
        

        $password = explode(' ', $data)[0];
        $email = explode(' ', $data)[1];

        die;
        User::create([
            'password' => $password,
            'email' => $email
            
        ]);
    }
}