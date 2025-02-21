<?php

namespace App\Debug;

use App\Host\HostService as Host;
use App\User\UserService as Auth;
use Lib\Session;

class DebugController
{

    public function dump()
    {
        $data = parse_request('data')[0];

        // Host vars
        $level = Host::data()->level;
        $root_pass = Host::data()->password;
        $min_level = $level->min;
        $max_level = $level->max;

        // Number of rows and columns in the memory dump
        $rows = 17;
        $columns = 4;

        // min: 5 max: 17
        $max_words = $max_level;
        $max_attempts = 4;
    
        if (!Session::has('root_pass')) {
            Session::set('root_pass', $root_pass);
        } 
        
        $pass_length = strlen($root_pass); 
        $root_pass = Session::get('root_pass');
    
        // Initialize attempts if not already set
        if (!Session::has('debug_attempts')) {
            Session::set('debug_attempts', $max_attempts);
        }
    
        if (!Session::has('dump')) {
            $word_list = wordlist($pass_length, $max_words, 'password-list.txt');
            $data = array_merge([$root_pass], $word_list);
    
            // Generate the memory dump
            $memoryDump = mem_dump($rows, $columns, $data, $pass_length);
    
            // Format and output the memory dump with memory paths
            /*
            if (!Session::has('debug')) {
                echo $this->reset();
            }
            */
            
            // $attempts = Session::get('debug_attempts');
            // echo "{$attempts} ATTEMPT(S) LEFT: # # # # \n \n";
    
            Session::set('dump', format_dump($memoryDump));
            echo Session::get('dump');
            exit;
        } else {
    
            if ($data != $root_pass) {

                $debug_attempts = Session::get('debug_attempts');

                $match = count_match_chars($data, $root_pass);

                Session::set('dump', 
                    str_replace($data, dot_replacer($data), Session::get('dump'))
                );
    
                if(preg_match('/\([^()]*\)|\{[^{}]*\}|\[[^\[\]]*\]|<[^<>]*>/', $data)) {
                    if($debug_attempts < 4) {
                        Session::set('debug_attempts', ++$debug_attempts);
                    }
                    
                } else {
                    Session::set('debug_attempts', --$debug_attempts);
                }

                if(!Session::has('user_blocked')) {
                    // echo "{$match}/{$pass_length} match.\n";

                    // $attempts_left = str_char_repeat($debug_attempts);
    
                    // echo "{$debug_attempts} ATTEMPT(S) LEFT: {$attempts_left} \n \n";
                }

                if (Session::get('debug_attempts') === 1) {
                    echo "--LOCKOUT IMMINENT--\n\n";
                }

    
                if (Session::get('debug_attempts') <= 0) {
                    echo 'Access Denied. This incident will be reported.';
                    Session::set('user_blocked', true);
                    exit;
                }
    
                echo Session::get('dump');
                exit;
            } else {
                
                // Store the new user credentials
                $server_id = Host::data()->id;
                if(!Auth::data()->host(Host::data()->id)) {
                    Auth::data()->hosts()->attach($server_id);
                }

                $reset_root_pass = wordlist($min_level, 1, 'password-list.txt')[0];
                Host::data()->update(['password' => $reset_root_pass]);

                // Reset login attempts on successful login
                Session::remove('debug_attempts');
                Session::remove('user_blocked');
                Session::remove('root_pass');
                Session::remove('dump');
    
                sleep(1);
                echo strtoupper(Auth::username()) . ' added to system successfully';
            }
    
        }
    }

    public function reset()
    {
        $term_mode = Session::get('term');

        return <<< EOT
        SECURITY RESET...

        WELCOME TO TELETERM

        >SET TERMINAL/INQUIRE
        
        $term_mode
        
        >SET FILE/PROTECTION=OWNER:RWED ACCOUNTS.F
        >SET HALT RESTART/MAINT
        
        Initializing AT&T MF Boot Agent v2.3.0
        RETROS BIOS
        RBIOS-4.02.08.00 52EE5.E7.E8
        Copyright 1975-1977 AT&T
        Uppermem: 64 KB
        Root (5A8)
        Maintenance Mode
        
        >RUN DEBUG/ACCOUNTS.F
        
        TELETERM PROTOCOL
        ENTER PASSWORD NOW

        
        EOT;
    }

    public function set() 
    {
        $data = request()->get('data');

        if(empty($data)) {
            echo 'ERROR: Missing Parameters!';
            exit;
        }
    
        $command = strtoupper($data);
    
        if(strpos('TERMINAL/INQUIRE', $command) !== false) {
            echo 'RIT-V300'. "\n";
            exit;
        }
    
        if(strpos('FILE/PROTECTION=OWNER:RWED ACCOUNTS.F', $command) !== false) {
            Session::set('root', true);
            echo "Root (5A8) \n";
            exit;
        }
    
        if(strpos('HALT', $command) !== false) {
            Auth::logout();
            echo 'SHUTTING DOWN...';
            exit;
        }
    
        if(strpos('HALT RESTART', $command) !== false) {
            echo 'RESTARTING...';
            text('boot.txt') . "\n";
            exit;
        }
    
        if(strpos('HALT RESTART/MAINT', $command) !== false) {
            Session::set('maint', true);
            text('maint.txt') . "\n";
            exit;
        }
       
    }

    public function run() { 

        $data = request()->get('data');

        if(empty($data)) {
            echo 'ERROR: Missing Parameters!';
            exit;
        }
    
        $command = strtoupper($data);
    
        if(!isset($_SESSION['root'])) {
            echo 'ERROR: Root Access Required!';
            exit;
        }
        
        if(!isset($_SESSION['maint'])) {
            echo 'ERROR: Maintenance Mode Required!';
            exit;
        }
    
        if(strpos('LIST/ACCOUNTS.F', $command) !== false) {
            //return listAccounts();
        }
    
        if(strpos('DEBUG/ACCOUNTS.F', $command) !== false) {
            session()->set('debug', true);
            Session::set('debug', true);
            text('attempts.txt') . "\n";
            echo $this->dump();
            exit;
        }
    }
  
}