<?php

namespace App\Controllers\Traits;

use App\Models\Host;
use App\Models\User;

trait DebugTrait 
{

    public function dump()
    {
        $data = request()->get('data');

        $level = host()->server()->level;
        $min_level = $level->min;
        $max_level = $level->max;

        $data = trim(strtoupper($data));
        // min: 5 max: 17
        $max_words = rand($min_level, $max_level);
        $max_attempts = 4;
    
        if (!isset($_SESSION['debug_pass'])) {
    
            // min: 2 max: 15
            $_SESSION['word'] = rand($min_level, $max_level);
            $_SESSION['debug_pass'] = wordlist(config('views') . '/lists/wordlist.txt', $_SESSION['word'] , 1)[0];
        } 
        
        $word_length = $_SESSION['word']; 
        $debug_pass = $_SESSION['debug_pass'];
    
        // Initialize attempts if not already set
        if (!isset($_SESSION['debug_attempts'])) {
            $_SESSION['debug_attempts'] = $max_attempts;
        }
    
        if (!isset($_SESSION['dump'])) {
            $word_list = wordlist(config('views') . '/lists/wordlist.txt', $word_length, $max_words);
            $data = array_merge([$debug_pass], $word_list);
    
            // Number of rows and columns in the memory dump
            $rows = 17;
            $columns = 2;
    
            // Generate the memory dump
            $memoryDump = mem_dump($rows, $columns, $data, $word_length);
    
            // Format and output the memory dump with memory paths
            if (!isset($_SESSION['debug'])) {
                view('/terminal/debug.txt');
            }
    
            echo "{$_SESSION['debug_attempts']} ATTEMPT(S) LEFT: # # # # \n \n";
    
            $_SESSION['dump'] = format_dump($memoryDump);
            echo $_SESSION['dump'];
            exit;
        } else {
    
            if ($data != $debug_pass) {
                $match = count_match_chars($data, $debug_pass);
                $_SESSION['dump'] = str_replace($data, dot_replacer($data), $_SESSION['dump']);
    
                if(preg_match('/\([^()]*\)|\{[^{}]*\}|\[[^\[\]]*\]|<[^<>]*>/', $data)) {
                    echo "Dud Removed.\n";
                    echo "Tries Reset.\n";
    
                    if($_SESSION['debug_attempts'] < 4) {
                        $_SESSION['debug_attempts']++;
                    }
                }
    
                if(preg_match('/^[a-zA-Z]+$/', $data)) {
                    $_SESSION['debug_attempts']--;
                }
    
                if(!isset($_SESSION['user_blocked'])) {
                    echo "Entry denied.\n";
                    echo "{$match}/{$word_length} correct.\n";
                    echo "Likeness={$match}.\n \n";

                    $attemps_left = str_char_repeat($_SESSION['debug_attempts']);
    
                    echo "{$_SESSION['debug_attempts']} ATTEMPT(S) LEFT: {$attemps_left} \n \n";
                }

                if ($_SESSION['debug_attempts'] === 1) {
                    echo "!!! WARNING: LOCKOUT IMMINENT !!!\n\n";
                }

    
                if ($_SESSION['debug_attempts'] <= 0) {
                    $_SESSION['user_blocked'] = true;
                    echo "ERROR: TERMINAL LOCKED.\nPlease contact an administrator\n";
                    exit;
                }
    
                echo $_SESSION['dump'];
                exit;
            } else {
                
                // Store the new user credentials
                $server_id = host()->server()->id;
                if(!auth()->user()->host($server_id)) {
                    auth()->user()->hosts()->attach($server_id);
                }

                host()->debug($debug_pass, auth()->user()->id);

                // Reset login attempts on successful login
                unset($_SESSION['debug_attempts']);
                unset($_SESSION['user_blocked']);
                unset($_SESSION['debug_pass']);
                unset($_SESSION['word']);
                unset($_SESSION['dump']);
                
    
                echo "EXCACT MATCH!\n";
                echo "+0050 XP \n";
                echo "LOADING...";
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
            session()->set('root', true);
            echo "Root (5A8) \n";
            exit;
        }
    
        if(strpos('HALT', $command) !== false) {
            $this->user->logout();
            
            echo 'SHUTTING DOWN...';
            exit;
        }
    
        if(strpos('HALT RESTART', $command) !== false) {
            echo 'RESTARTING...';
            echo view('terminal/boot.txt') . "\n";
            exit;
        }
    
        if(strpos('HALT RESTART/MAINT', $command) !== false) {
            session()->set('maint', true);
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
            return listAccounts();
        }
    
        if(strpos('DEBUG/ACCOUNTS.F', $command) !== false) {
            session()->set('debug', true);
            echo view('terminal/attempts.txt') . "\n";
            echo $this->dump();
            exit;
        }
    }
  
}