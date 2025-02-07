<?php

namespace App\System;

use Lib\Controller;
use Lib\Session;

use App\User\UserService as User;
use App\User\UserModel as Users;
use App\Host\HostService as Host;
use App\System\SystemService as System;
use App\System\CronService as Cron;

class SystemController extends Controller
{
    public function boot() 
    {
        echo bootup() . "\n\n";
        echo text('boot.txt');
    }

    public function version() 
    {
        echo text('version.txt');
    }

    public function mode()
    {
        $data = parse_request('data');
        Session::set('term', strtoupper($data[0]));
    }

    public function home()
    {
        Session::set('term', 'RIT-V300');
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
        }

        $data = parse_request('data');
        return System::uplink($data[0]);

    }

    public function minify()
    {
        return Cron::minify();
    }

    public function stats()
    {
        return Cron::stats();
    }

}