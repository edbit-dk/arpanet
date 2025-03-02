<?php

namespace Lib;

class Passwd
{
    public static $file;

    // format "root:5d41402abc4b2a76b9719d911017c592:0:0:Superuser:/root:/bin/sh\n"
    public static function file($string)
    {
        self::$file = $string;
    }

    // Actions: edit, delete, add
    public static function set($action, $username, $newLine = null)
    {
        // Split the content into lines
        $lines = explode(PHP_EOL, self::$file);
        
        // Loop through the lines and process based on the action
        foreach ($lines as $index => $line) {
            // Split each line into parts (assuming ':' as separator)
            $parts = explode(':', $line);
            
            // Check if the username matches
            if ($parts[0] === $username) {
                if ($action === 'edit' && $newLine !== null) {
                    // Edit the line by replacing it with the new line
                    $lines[$index] = $newLine;
                } elseif ($action === 'delete') {
                    // Remove the line
                    unset($lines[$index]);
                }
                return implode(PHP_EOL, $lines);  // Return updated content after modification
            }
        }

        // If action is 'add', add a new line
        if ($action === 'add' && $newLine !== null) {
            $lines[] = $newLine;
            return implode(PHP_EOL, $lines);  // Return updated content after adding
        }

        return self::$file;  // Return original content if no action was performed
    }

    public static function get($username, $field = 1)
    {
        // Split the content into lines
        $lines = explode(PHP_EOL, self::$file);
        
        foreach ($lines as $line) {
            // Split each line into parts (assuming ':' as separator)
            $parts = explode(':', $line);
            
            // Check if the username matches
            if ($parts[0] === $username) {
                return $parts[$field];  // Return the password (second field in the line)
            }
        }

        return null;  // Return null if the username wasn't found
    }
}