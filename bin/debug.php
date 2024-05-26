<?php

function wordlist($file, $word_length = 7) {
    $words = file_get_contents($file);
    
    $words = explode(" ", $words);
    $retwords = [];
    $i=0;
    $index=0;
    $wordlen=0;
    $length = $word_length;
    $count =12;
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

    $data = strtoupper($data);
    $root_pass = $server['pass'];
    $word_length = strlen($root_pass);

    if (!isset($_SESSION['dump'])) {
        $setup = file_get_contents('sys/var/debug.txt');

        $word_list = wordlist('sys/var/wordlist.txt', $word_length);
        $passwords[] = $root_pass;
        //$usernames = array_keys($server['accounts']);
        $data = array_merge($passwords, $word_list);

        // Number of rows and columns in the memory dump
        $rows = 17;
        $columns = 4;

        // Specific words to include in the memory dump
        $specialWords = $data;

        // Generate the memory dump
        $memoryDump = mem_dump($rows, $columns, $specialWords, $word_length);

        // Format and output the memory dump with memory paths
        echo $setup . "\n";

        $_SESSION['dump'] = format_dump($memoryDump);
        return $_SESSION['dump'];
    } else {

        if($data != strtoupper($root_pass)) {
            $_SESSION['dump'] = str_replace($data, replaceWithDots($data), $_SESSION['dump']);
            return $_SESSION['dump'];
        }

        return $_SESSION['dump'];

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
        $memoryAddress = "0x" . dechex(rand(4096, 65535));
        $formattedDump .= $memoryAddress . " ";
        foreach ($row as $cell) {
            $formattedDump .= " " . $cell;
        }
        $formattedDump .= "\n";
    }

    return $formattedDump;
}
