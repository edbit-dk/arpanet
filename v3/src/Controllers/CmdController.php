<?php

namespace App\Controllers;

use App\Providers\Controller;

use App\Models\Host;

class CmdController extends Controller
{

    public function help()
    {
        $command = strtoupper(request()->get('data'));

        if(!auth()->check()) {
            $help = require config('path') . '/storage/array/auth.php';
        } else {
            $help = require config('path') . '/storage/array/guest.php';
        }
    

        if (!empty($command)) {
            return isset($help[$command]) ? $help[$command] : "Command not found.";
        }
        
        $output = "COMMANDS:\n";
        foreach ($help as $cmd => $text) {
            $output .= " $cmd $text\n";
        }
        echo $output;
    }

    public function user() 
    {
        $user = $this->user->auth();

        echo "USERNAME: {$user->username} \n";
        echo "FIRSTNAME: {$user->firstname} \n";
        echo "LASTNAME: {$user->lastname} \n";
        echo "LEVEL: {$user->level_id} \n";
        echo "XP: {$user->xp} \n";
        echo "REP: {$user->rep} \n";
    }

    public function hosts() 
    {
        $servers = auth()->hosts()->get();

        foreach ($servers as $server) {

            $name = $server->name;
            $ip = $server->ip;

            echo "[$name ($ip)]\n";
        }
    }

    public function scan() 
    {
        if(host()->auth()) {
            $nodes = host()->server()->nodes()->get();
            dd($nodes);
        }

        $servers  = Host::inRandomOrder()->limit(5)->get();

        foreach ($servers as $server) {

            $name = $server->name;
            $ip = $server->ip;

            echo "[$name ($ip)]\n";
        }
        
    }
}