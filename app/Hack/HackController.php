<?php

namespace App\Hack;

use App\AppController;

use Lib\Dump;
use Lib\Crypt;
use Lib\DES;
use Lib\Enigma;
use Lib\Passwd;
use Lib\RSA;

use App\Host\HostService as Host;

class HackController extends AppController
{

    public function dump()
    {
        Dump::correct([Host::admin(), Host::password()]);

        if($input = $this->data) {
            Dump::input($input);
        }
        Dump::memory();
    }

}