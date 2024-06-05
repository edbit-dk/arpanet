<?php

function wordlist($file, $word_length = 7, $max_count = 12) {
    $words = file_get_contents($file);
    
    $words = explode(" ", $words);
    $retwords = [];
    $i=0;
    $index=0;
    $wordlen=0;
    $length = $word_length;
    $count =$max_count;
    $failsafe=0;
    
    do {
        $index = rand(0,count($words));
        $wordlen = strlen($words[$index]);
        if ($wordlen == $length) {
            $retwords[] = strtoupper($words[$index]);
            $i++;
        } else {
            $failsafe++;
        }
        if ($failsafe > 1000) $i = $failsafe;
    } while ($i < $count);
    
    //$retwords = substr($retwords,0,strlen($retwords)-1);
    return $retwords;
}

function dump($data) {
    global $server;

    $data = trim(strtoupper($data));
    $max_words = rand(5, 17);
    $max_attempts = 4;

    if (!isset($_SESSION['DEBUG_PASS'])) {

        $_SESSION['WORD'] = rand(3, 13);
        $_SESSION['DEBUG_PASS'] = wordlist('sys/var/wordlist.txt', $_SESSION['WORD'] , 1)[0];
    } 
    
    $word_length = $_SESSION['WORD']; 
    $admin_pass = $_SESSION['DEBUG_PASS'];

    // Initialize attempts if not already set
    if (!isset($_SESSION['ATTEMPTS'])) {
        $_SESSION['ATTEMPTS'] = $max_attempts;
    }

    if (!isset($_SESSION['DUMP'])) {
        $word_list = wordlist('sys/var/wordlist.txt', $word_length, $max_words);
        $data = array_merge([$admin_pass], $word_list);

        // Number of rows and columns in the memory dump
        $rows = 17;
        $columns = 4;

        // Generate the memory dump
        $memoryDump = mem_dump($rows, $columns, $data, $word_length);

        // Format and output the memory dump with memory paths
        if (!isset($_SESSION['DEBUG_MODE'])) {
            echo file_get_contents('sys/var/debug.txt');
        }

        echo "{$_SESSION['ATTEMPTS']} ATTEMPT(S) LEFT: # # # # \n \n";

        $_SESSION['DUMP'] = format_dump($memoryDump);
        return $_SESSION['DUMP'];
    } else {

        if ($data != $admin_pass) {
            $match = count_match_chars($data, $admin_pass);
            $_SESSION['DUMP'] = str_replace($data, replaceWithDots($data), $_SESSION['DUMP']);

            if(preg_match('/\([^()]*\)|\{[^{}]*\}|\[[^\[\]]*\]/', $data)) {
                echo "Dud Removed.\n";
                echo "Tries Reset.\n";
                $_SESSION['ATTEMPTS']++;
            }

            if(preg_match('/^[a-zA-Z]+$/', $data)) {
                $_SESSION['ATTEMPTS']--;
            }

            echo "Entry denied.\n";
            echo "{$match}/{$word_length} correct.\n";
            echo "Likeness={$match}.\n \n";

            if ($_SESSION['ATTEMPTS'] === 1) {
                echo "WARNING: Lockout Imminent !!!\n\n";
            }

           $attemps_left = str_char_repeat($_SESSION['ATTEMPTS']);

            echo "{$_SESSION['ATTEMPTS']} ATTEMPT(S) LEFT: {$attemps_left} \n \n";

            if ($_SESSION['ATTEMPTS'] <= 0) {
                $_SESSION['BLOCKED'] = true;
                return "ERROR: Terminal Locked.\n\nPlease contact an administrator!\n";
            }

            return $_SESSION['DUMP'];
        } else {
            // Reset login attempts on successful login
            unset($_SESSION['ATTEMPTS']);
            unset($_SESSION['BLOCKED']);

            // Add one to the XP field
            if (isset($_SESSION['USER'])) {
                $user_id = $_SESSION['USER']['ID'];
                $_SESSION['USER']['XP'] += 50;
                file_put_contents("user/{$user_id}.json", json_encode($_SESSION['USER']));
            }

            echo "EXCACT MATCH!\n";
            return "PASSWORD: {$admin_pass}\n";
        }

    }
}


function replaceWithDots($input) {
    // Get the length of the input string
    $length = strlen($input);
    
    // Create a string of dots with the same length as the input string
    $dots = str_repeat('.', $length);
    
    return $dots;
}


// Function to generate a random string of characters
function rand_str($length = 7) {
    global $special_chars;
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $special_chars[rand(0, strlen($special_chars) - 1)];
    }
    return $randomString;
}


// Function to generate a memory dump
function mem_dump($rows, $columns, $specialWords = [], $length = 7) {
    $memoryDump = array();

    // Insert special words into the specialPositions array
    $specialPositions = [];
    for ($i = 0; $i < count($specialWords); $i++) {
        $row = rand(0, $rows - 1);
        $col = rand(0, $columns - 1);
        $specialPositions[] = [$row, $col, strtoupper($specialWords[$i])];
    }

    // Generate random strings for each cell
    for ($i = 0; $i < $rows; $i++) {
        $row = array();
        for ($j = 0; $j < $columns; $j++) {
            $cell = rand_str($length);
            // Check if this cell is a special position
            foreach ($specialPositions as $index => $pos) {
                if ($pos[0] === $i && $pos[1] === $j) {
                    // Insert special word and remove it from specialPositions array
                    $cell = $pos[2];
                    unset($specialPositions[$index]);
                    break;
                }
            }
            $row[] = $cell;
        }
        $memoryDump[] = $row;
    }

    return $memoryDump;
}

// Function to format the memory dump with memory paths
function format_dump($memoryDump) {
    $formattedDump = "";
    $rowNumber = 0;

    foreach ($memoryDump as $row) {
        // Generate a random starting memory address for each line
        $memoryAddress = "0x" . dechex(rand(4096, 6553));
        $formattedDump .= $memoryAddress . " ";
        foreach ($row as $cell) {
            $formattedDump .= " " . $cell;
        }
        $formattedDump .= "\n";
    }

    return $formattedDump;
}


function set($data) {

    $command = strtoupper($data);

    if(strpos('TERMINAL/INQUIRE', $command) !== false) {
        return 'RIT-V300'. "\n";
    }

    if(strpos('FILE/PROTECTION=OWNER:RWED ACCOUNTS.F', $command) !== false) {
        $_SESSION['ROOT_ACCOUNT'] = true;
        return "Root (5A8) \n";
    }

    if(strpos('HALT RESTART/MAINT', $command) !== false) {
        $_SESSION['MAINT_MODE'] = true;
        return file_get_contents('sys/var/maint.txt') . "\n";
    }

}

function run($data) { 

    $command = strtoupper($data);

    if(!isset($_SESSION['ROOT_ACCOUNT'])) {
        return 'ERROR: Root Access Required!';
    }
    
    if(!isset($_SESSION['MAINT_MODE'])) {
        return 'ERROR: Maintenance Mode Required!';
    }

    if(strpos('DEBUG/ACCOUNTS.F', $command) !== false) {
        $_SESSION['DEBUG_MODE'] = true;
        echo file_get_contents('sys/var/attempts.txt') . "\n";
        return dump($data);
    }
}
