<?php

namespace App\Controllers;

use App\Services\Controller;

use App\Models\User;
use App\Models\Server;

class ServerController extends Controller
{
    public function connect($data) {
        $server_id = explode(' ', $data)[0];

        if (file_exists(APP_CACHE . "server/{$server_id}.json")) {
            $this->logoff();  
            return "Contacting Server: {$server_id}\n";
        } else {
            return 'ERROR: ACCESS DENIED';
        }
    }

    public function logoff() {

        $server = $this->mainframe->server();

        $this->mainframe->logout();
    
        return "LOGGING OUT FROM {$server}...\n";
    }
}