<?php

namespace App;

use Lib\Controller;
use Lib\Input;

use App\User\UserService as User;
use App\Host\HostService as Host;

use App\AppService as App;
use App\System\SystemService as System;

class AppController extends Controller
{
    protected $request;
    protected $data = false;

    public function __construct()
    {
        if($data = Input::get('data')) {
            $this->data = $data;
        }

        $this->request = Input::request();

    }

    public function version()
    {
        App::version();
    }

    public function music()
    {
        exit;
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
            return System::uplink($this->data);
        }

    }
    
}