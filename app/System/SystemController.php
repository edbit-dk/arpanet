<?php

namespace App\System;

use App\AppController;
use App\System\SystemService as System;

use Lib\Session;

class SystemController extends AppController
{
    public function boot() 
    {
        System::boot();
    }

    public function mode()
    {
        if($type = $this->data) {
            System::mode($type);
        }
    }
}