<?php

namespace Lib;

use \Lib\Session;

class Dump 
{

    public static $reset = false;
    public static $default = ["HACK", "PASSWORD", "SECURITY", "VAULT", "ACCESS", "DENIED", "TERMINAL", "ADMIN", "PASS"];
    public static $words = [];
    public static $correct = ["ADMIN", "PASS"];
    public static $dump = 'memory_dump';
    public static $input = 'memory_input';

    public static function reset()
    {
        self::$reset = true;
        Session::remove(self::$input);
        Session::remove(self::$dump);
    }

    public static function words($words = [])
    {
        self::$words = $words;
    }

    public static function correct($words = [])
    {
        self::$correct = $words;
    }

    public static function memory($rows = 16, $cols = 8) 
    {
        if(empty(self::$words)) {
            self::$words = ["HACK", "PASSWORD", "SECURITY", "VAULT", "ACCESS", "DENIED", "TERMINAL", "ADMIN", "PASS"];
        }
        
        // Setup
        $words = array_merge(self::$words, self::$correct);
        $randomize = self::$reset;

        $hexBase = 0x4000; // Base address for memory dump
        $output = "";
        
        // Convert words to uppercase for consistency
        $totalCells = $rows * $cols * 2; // Each hex cell is two characters
        
        // If randomize is true, shuffle special words
        if ($randomize && Session::has(self::$dump) && Session::has(self::$input)) {
            Session::remove(self::$input);
            Session::remove(self::$dump);
        }
        
        // Check if memory dump already exists in session
        if (!Session::has(self::$dump)) {
            $hexData = [];

            // Define allowed symbols (no letters or numbers)
            $allowedSymbols = array_merge(range(33, 47), range(58, 64), range(91, 96), range(123, 126));

            // Fill hexData with random symbols only
            for ($i = 0; $i < $totalCells; $i++) {
                $randByte = $allowedSymbols[array_rand($allowedSymbols)]; // Pick a random symbol
                $hexData[$i] = sprintf("%02X", $randByte); // Convert to hex
            }

            // Insert special words at predefined locations
            $usedPositions = [];
            foreach ($words as $word) {
                $wordHex = bin2hex($word);
                $wordLength = strlen($wordHex) / 2;

                // Find a non-overlapping position
                do {
                    $position = rand(0, $totalCells - $wordLength);
                } while (array_intersect(range($position, $position + $wordLength - 1), $usedPositions));

                // Mark this position as used
                $usedPositions = array_merge($usedPositions, range($position, $position + $wordLength - 1));

                // Insert the word into the hexData
                for ($i = 0; $i < strlen($wordHex); $i += 2) {
                    $hexData[$position + ($i / 2)] = substr($wordHex, $i, 2);
                }
            }

            // Store the generated memory dump in session
            Session::set(self::$dump, $hexData);
        } else {
            // Load existing memory dump (ensuring it stays unchanged)
            $hexData = Session::get(self::$dump);
        }

        // Track incorrect guesses in session
        if (!Session::has(self::$input)) {
            Session::set(self::$input, []);
        }

        // Generate memory dump output
        $output = '';
        for ($i = 0; $i < $rows; $i++) {
            $address = sprintf("%04X", $hexBase + ($i * $cols * 2));
            $hexRow = array_slice($hexData, $i * $cols * 2, $cols * 2);

            // Convert hex to ASCII
            $asciiRow = '';
            for ($j = 0; $j < count($hexRow); $j++) {
                $byte = hexdec($hexRow[$j]);
                $char = ($byte >= 32 && $byte <= 126) ? chr($byte) : '.';
                $asciiRow .= $char;
            }

            // Hide incorrect guesses in ASCII
            foreach (Session::get(self::$input) as $wrongWord) {
                if (strpos($asciiRow, $wrongWord) !== false) {
                    $asciiRow = preg_replace('/\b' . preg_quote($wrongWord, '/') . '\b/', str_repeat('.', strlen($wrongWord)), $asciiRow);
                }
            }

            // Format the output
            $output .= "$address  " . implode(" ", str_split(implode("", $hexRow), 2)) . "  |$asciiRow|\n";
        }
        
        echo $output;
    }

    public static function input($word) 
    {
        $correct = self::$correct;

        $input = $word;
        
        if (in_array($input, $correct)) {
            return true;
        } else {
            // Store incorrect guesses in session
            if (!in_array($input, Session::get(self::$input))) {
                Session::set(self::$input, array_merge(Session::get(self::$input), [$input]));
            }
            return false;
        }
    }
}
