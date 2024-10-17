<?php

namespace App\Controllers;

use App\Providers\Controller;

use App\Models\Host;

class CmdController extends Controller
{

    public function hosts() 
    {
        $servers = auth()->hosts()->get();

        foreach ($servers as $server) {

            $name = $server->name;
            $ip = $server->ip;

            echo "[$name ($ip)]\n";
        }
    }

}