<?php

namespace App\Debug;

use App\AppController;

use Lib\Dump;
use Lib\Crypt;
use Lib\DES;
use Lib\Enigma;
use Lib\Passwd;
use Lib\RSA;

use App\Host\HostService as Host;

class DebugController extends AppController
{

    public function dump()
    {
        Dump::correct([Host::admin(), Host::password()]);

        if($input = $this->data) {
            if($input == 'reset') {
                Dump::reset();
                return Dump::memory();
            }
            Dump::input($input);
        }

        Dump::memory();
    }

}