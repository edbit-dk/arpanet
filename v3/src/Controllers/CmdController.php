<?php

namespace App\Controllers;

use App\Providers\Controller;

class CmdController extends Controller
{

    public function help()
    {
        $command = strtoupper(request()->get('data'));

        if(!$this->auth->check()) {
            $help = include($this->settings['path'] . '/app/storage/array/auth.php');
        } else {
            $help = include($this->settings['path'] . '/app/storage/array/guest.php');
        }
    

        if (!empty($command)) {
            return isset($help[$command]) ? $help[$command] : "Command not found.";
        }
        
        $output = "COMMANDS:\n";
        foreach ($help as $cmd => $text) {
            $output .= " $cmd $text\n";
        }
        return $output;
    }

    public function user() 
    {
        $user = $this->auth->user();

        echo "EMPLOYEE-ID: {$user->username} \n";
        echo "FIRSTNAME: {$user->firstname} \n";
        echo "LASTNAME: {$user->lastname} \n";
        echo "LEVEL: {$user->level_id} \n";
        echo "XP: {$user->xp} \n";
        echo "REP: {$user->rep} \n";
    }

    public function servers() 
    {
        $servers = $this->auth->user()->servers;

        foreach ($servers as $server) {
            echo $server->name . "\n";
        }
    }
}