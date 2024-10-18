<?php

namespace App\Controllers;

use App\Providers\Controller;

use App\Models\Host;
use App\Models\Level;

class HostController extends Controller
{

    public function connect() 
    {
        $this->logoff();

        $data = strtoupper(request()->get('data'));

        $server = host()->connect($data);

        sleep(1);
        
        if(!$server) {
            echo 'ERROR: ACCESS DENIED.';
            exit;
        } else {
            echo "Contacting Host...\n";
            exit;
        }

    }

    public function create() 
    {
        $data = request()->get('data');

        $input = explode(' ', trim($data));

        $name = $input[0];

        $level = Level::inRandomOrder()->first();

        $pass_length = rand($level->min, $level->max);
        
        $admin_pass = wordlist(config('views') . '/lists/wordlist.txt', $pass_length , 1)[0];

        $location = wordlist(config('views') . '/lists/statelist.txt', $pass_length , 1)[0];
        
        $host = Host::create([
            'name' => $name,
            'password' =>  strtolower($admin_pass),
            'level_id' => $level->id,
            'location' => $location,
            'ip' => random_ip()
        ]);

        echo 'OK';
    }

    // sysadmin571_bypas /: 
    public function sysadmin()
    {
        echo bootup();
    }

    public function scan() 
    {
        if(host()->auth() OR host()->guest()) {
            $servers = host()->server()->nodes()->get();
        } else {
            $servers  = Host::inRandomOrder()->limit(5)->get();
        }

        echo "Scanning...\n \n";

        foreach ($servers as $server) {

            $id = $server->id;
            $org = $server->org;
            $name = $server->name;
            $location = $server->location;

            echo "$id. $name [$org] ($location)\n";
        }
        
    }

    public function logoff() {

        $this->host->logout();
        unset($_SESSION['debug_pass']);
        unset($_SESSION['debug_attempts']);
        unset($_SESSION['user_blocked']);
        unset($_SESSION['dump']);
        unset($_SESSION['root']);
        unset($_SESSION['maint']);
    }
}