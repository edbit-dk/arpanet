<?php

// Function to handle user logout
function restartServer() {
    logoutUser();
    return "RESTARTING...";
}

// Function to display Message of the Day
function boot() {
    include('sys/var/boot.txt');
}

function motd() {
    require('sys/lib/motd.php');
}

// Function to log messages
function logMessage($message, $logFile = 1) {

    $logFile = "log/{$logFile}.log";

    // Set the default timezone
    date_default_timezone_set('UTC');

    // Open the log file in append mode
    $fileHandle = fopen($logFile, 'a');

    if ($fileHandle) {
        // Format the message with a timestamp
        $timestamp = date('Y-m-d H:i:s');
        $formattedMessage = "[$timestamp] $message" . PHP_EOL;

        // Write the message to the log file
        fwrite($fileHandle, $formattedMessage);

        // Close the file handle
        fclose($fileHandle);
    } else {
        // Handle error if the file could not be opened
        error_log("Could not open log file for writing.");
    }
}