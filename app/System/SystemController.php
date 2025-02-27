<?php

namespace App\System;

use Lib\Controller;
use Lib\Session;


use App\User\UserService as User;
use App\User\UserModel as Users;
use App\Host\HostService as Host;
use App\System\SystemService as System;
use App\System\CronService as Cron;
use App\System\SetupService as Setup;

class SystemController extends Controller
{
    public function boot() 
    {
        echo bootup(loops: 10) . "\n\n";
        echo <<< EOT
        Initializing boot...
        Loading TeleTerm OS...
        64K RAM detected...
        Launching Interfaces...

        Boot Complete.

        LOADING...
        EOT;
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
        Session::set('term', 'DEC-VT100');
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
            $data = parse_request('data');
            return System::uplink($data[0]);
        }

    }

    public function minify()
    {
        return Cron::minify();
    }

    public function stats()
    {
        return Cron::stats(1);
    }

    public function install()
    {
       return Setup::install();
    }

    public function system()
    {
       return Setup::system();
    }

    public function users()
    {
       return Setup::users();
    }

    public function hosts()
    {
       return Setup::hosts();
    }

    public function relations()
    {
       return Setup::relations();
    }

    public function folders()
    {
       return Setup::folders();
    }

    public function files()
    {
       return Setup::files();
    }

}