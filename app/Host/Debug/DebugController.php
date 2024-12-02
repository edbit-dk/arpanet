<?php

namespace App\Host\Debug;

use App\Host\HostService as Host;
use App\User\UserService as Auth;
use Lib\Session;
use Lib\Request;

class DebugController
{

    public function dump()
    {
        $data = request()->get('data');

        $level = Host::data()->level;
        $min_level = $level->min;
        $max_level = $level->max;

        $data = trim(strtoupper($data));
        // min: 5 max: 17
        $max_words = $max_level;
        $max_attempts = 4;
    
        if (!Session::has('debug_pass')) {
    
            // min: 2 max: 15
            Session::set('word', $min_level);
            Session::set('debug_pass',
            wordlist(config('database') . 'wordlist.txt', Session::get('word'), 1)[0]
            );
        } 
        
        $word_length = Session::get('word'); 
        $debug_pass = Session::get('debug_pass');
    
        // Initialize attempts if not already set
        if (!Session::has('debug_attempts')) {
            Session::set('debug_attempts', $max_attempts);
        }
    
        if (!Session::has('dump')) {
            $word_list = wordlist(config('database') . 'wordlist.txt', $word_length, $max_words);
            $data = array_merge([$debug_pass], $word_list);
    
            // Number of rows and columns in the memory dump
            $rows = 17;
            $columns = 3;
    
            // Generate the memory dump
            $memoryDump = mem_dump($rows, $columns, $data, $word_length);
    
            // Format and output the memory dump with memory paths
            if (!Session::has('debug')) {
                view('/terminal/debug.txt');
            }
            $attempts = Session::get('debug_attempts');
            echo "{$attempts} ATTEMPT(S) LEFT: # # # # \n \n";
    
            Session::set('dump', format_dump($memoryDump));
            echo Session::get('dump');
            exit;
        } else {
    
            if ($data != $debug_pass) {

                $debug_attempts = Session::get('debug_attempts');

                $match = count_match_chars($data, $debug_pass);

                Session::set('dump', 
                    str_replace($data, dot_replacer($data), Session::get('dump'))
                );
    
                if(preg_match('/\([^()]*\)|\{[^{}]*\}|\[[^\[\]]*\]|<[^<>]*>/', $data)) {
                    echo "Dud Removed.\n";
                    echo "Tries Reset.\n";
                    
                    if($debug_attempts < 4) {
                        Session::set('debug_attempts', ++$debug_attempts);
                    }
                    
                } else {
                    Session::set('debug_attempts', --$debug_attempts);
                }

                if(!Session::has('user_blocked')) {
                    echo "Entry denied.\n";
                    echo "{$match}/{$word_length} correct.\n";
                    echo "Likeness={$match}.\n \n";

                    $attempts_left = str_char_repeat($debug_attempts);
    
                    echo "{$debug_attempts} ATTEMPT(S) LEFT: {$attempts_left} \n \n";
                }

                if (Session::get('debug_attempts') === 1) {
                    echo "!!! WARNING: LOCKOUT IMMINENT !!!\n\n";
                }

    
                if (Session::get('debug_attempts') <= 0) {
                    Session::set('user_blocked', true);
                    echo "ERROR: TERMINAL LOCKED.\nPlease contact an administrator\n";
                    exit;
                }
    
                echo Session::get('dump');
                exit;
            } else {
                
                // Store the new user credentials
                $server_id = Host::data()->id;
                if(!Auth::data()->host($server_id)) {
                    Auth::data()->hosts()->attach($server_id);
                }

                Host::debug($debug_pass, Auth::data()->id);

                // Reset login attempts on successful login
                Session::remove('debug_attempts');
                Session::remove('user_blocked');
                Session::remove('debug_pass');
                Session::remove('word');
                Session::remove('dump');
    
                echo "EXCACT MATCH!\n";
                echo "+0050 XP\n";
                echo "Adding user to host accounts...\n";
                echo "Please wait while system is accessed...\n";
            }
    
        }
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
            echo view('terminal/boot.txt') . "\n";
            exit;
        }
    
        if(strpos('HALT RESTART/MAINT', $command) !== false) {
            Session::set('maint', true);
            echo view('terminal/maint.txt') . "\n";
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
            view('terminal/attempts.txt') . "\n";
            echo $this->dump();
            exit;
        }
    }
  
}