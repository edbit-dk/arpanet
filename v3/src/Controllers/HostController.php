<?php

namespace App\Controllers;

use App\Providers\Controller;

class HostController extends Controller
{
    public function connect() 
    {
        $data = strtoupper(request()->get('data'));

        $server = $this->host->connect($data);

        sleep(1);
        
        if(!$server) {
            echo 'ERROR: ACCESS DENIED.';
            exit;
        } else {
            echo "Contacting host...\n";
            exit;
        }

    }

    public function logon() {

        $data = request()->get('data');

        $this->host->debug();
    
        $params = explode(' ', $data);
    
        // Initialize login attempts if not set
        $this->host->attempts();
    
        // Check if the user is already blocked
        $this->host->blocked();
    
        // If no parameters provided, prompt for username
        if (empty($params)) {
            echo "ERROR: Wrong Username.";
            exit;
        } else {
            $username = $params[0];
        }
    
        // If both username and password provided, complete login process
        if (count($params) === 2) {
            $username = strtolower($params[0]);
            $password = strtolower($params[1]);
    
            // Validate password
            if ($this->host->logon($username, $password)) {
    
                // Reset login attempts on successful login
                $this->host->reset();
    
                echo "Password Accepted.\nPlease wait while system is accessed...\n+0025 XP ";
                exit;
    
            } else {
    
                // Calculate remaining attempts
                $attempts_left = $this->host->attempts(true);
    
                if ($attempts_left === 1) {
                    echo "WARNING: Lockout Imminent !!!\n";
                }
    
                // Block the user after 4 failed attempts
                if ($attempts_left === 0) {
                    $this->host->block(true);
                    echo "TERMINAL LOCKED.\n";
                    echo "Please contact an administrator.";
                    exit;
                }
    
                echo "ERROR: Wrong Username or Password.\nAttempts Remaining: {$attempts_left}";
                exit;
            }
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
    
        echo "Disconnecting...\n";
    }
}