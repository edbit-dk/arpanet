<?php

namespace App\Controllers;

use App\Models\User;
use App\Services\Controller;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{

    public function index($request, $response) 
    {

        return $this->view->render($response, 'terminal.twig');
    }

    public function register($request, $response) 
    {
        
        $data = $request->getParam('data');

        if(empty($data)) {
            return 'ERROR: Missing parameters.';
        }

        $input = [
            'password' =>  explode(' ', $data)[0],
            'email' => explode(' ', $data)[1]
        ];

        $validation = false;

        if(!$validation) {
            return 'ERROR: Missing parameters.';
        }

        $user_id = User::create([
            'password' => $input['password'],
            'email' => $input['email']
            
        ])->id();

        $password = $input['password'];
        $email = $input['email'];

        return "ACCESS CODE: {$password}\nEMPLOYEE ID: {$email}\n";
    }
}