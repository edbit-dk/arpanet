<?php

namespace App\Controllers;

use App\Services\Controller;

use App\Models\User;

class CmdController extends Controller
{

    public function help() 
    {
        return 'ok';
    }
}