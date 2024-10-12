<?php

namespace App\Controllers;

use App\Providers\Controller;

class ServerController extends Controller
{
    public function connect() 
    {
        $data = strtoupper(request()->get('data'));

        $server = $this->mainframe->connect($data);

        sleep(1);
        
        if(!$server) {
            return 'ERROR: ACCESS DENIED.';
        } else {
            return "Contacting Server...\n";
        }

    }

    public function logon() {

        $data = request()->get('data');

        $this->mainframe->debug();
    
        $params = explode(' ', $data);
    
        // Initialize login attempts if not set
        $this->mainframe->attempts();
    
        // Check if the user is already blocked
        $this->mainframe->blocked();
    
        // If no parameters provided, prompt for username
        if (empty($params)) {
            return "ERROR: Wrong Username.";
        } else {
            $username = $params[0];
        }
    
        // If both username and password provided, complete login process
        if (count($params) === 2) {
            $username = strtolower($params[0]);
            $password = strtolower($params[1]);
    
            // Validate password
            if ($this->mainframe->attempt($username, $password)) {
    
                // Reset login attempts on successful login
                $this->mainframe->reset();
    
                return "Password Accepted.\nPlease wait while system is accessed...\n+0025 XP ";
    
            } else {
    
                // Calculate remaining attempts
                $attempts_left = $this->mainframe->attempts(true);
    
                if ($attempts_left === 1) {
                    echo "WARNING: Lockout Imminent !!!\n";
                }
    
                // Block the user after 4 failed attempts
                if ($attempts_left === 0) {
                    return $this->mainframe->block(true);
                }
    
                return "ERROR: Wrong Username or Password.\nAttempts Remaining: {$attempts_left}";
            }
        }
    }

    public function logoff() {

        $this->mainframe->logout();
    
        return "DISCONNECTING FROM SERVER...\n";
    }
}