<?php

namespace App;

use Lib\Controller;

use App\User\UserService as User;
use App\Host\HostService as Host;

use App\AppService as App;
use App\System\SystemService as System;

class AppController extends Controller
{
    public $request;

    public function __construct()
    {
        if($request = parse_request('data')) {
            $this->request = $request;
        }
    }

    public function version()
    {
        App::version();
    }

    public function home()
    {
        System::mode('DEC-VT100');
        view('app.php');
    }

    public function main() 
    {
        if(Host::guest()) {
            return System::connect();
        }

        if(Host::auth() == 1) {
            return System::user();
        }

        if(Host::auth() > 1) {
            return System::auth();
        }

        if(User::uplinked()) {
            return System::login();
        } else {
            return System::uplink($this->request[0]);
        }

    }
    
}