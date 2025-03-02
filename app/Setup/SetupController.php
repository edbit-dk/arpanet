<?php

namespace App\Setup;

use App\AppController;

use App\System\SetupService as Setup;

class SetupController extends AppController
{
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