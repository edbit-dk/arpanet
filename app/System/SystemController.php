<?php

namespace App\System;

use App\AppController;
use App\System\SystemService as System;

class SystemController extends AppController
{
    public function boot() 
    {
        System::boot();
    }

    public function mode()
    {
        if($type = parse_request('data')) {
            System::mode($type[0]);
        }
    }
}