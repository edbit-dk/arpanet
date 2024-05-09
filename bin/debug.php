<?php

function dump($data) {
    // Number of rows and columns in the memory dump
    $rows = 17;
    $columns = 6;

    // Specific words to include in the memory dump
    $specialWords = $data;

    // Generate the memory dump
    $memoryDump = mem_dump($rows, $columns, $specialWords);

// Format and output the memory dump with memory paths
echo "---------------------------------------------------------------------------------------\n";
echo format_dump($memoryDump);
}

function word_list() {
    include('sys/lib/wordlist.php');
}

// Function to generate a random string of characters
function rand_str($length = 12) {
    global $special_chars;
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $special_chars[rand(0, strlen($special_chars) - 1)];
    }
    return $randomString;
}


// Function to generate a memory dump
function mem_dump($rows, $columns, $specialWords = []) {
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
            $cell = rand_str();
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
            $formattedDump .= "" . $cell;
        }
        $formattedDump .= "\n";
    }

    return $formattedDump;
}
