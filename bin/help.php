<?php

// Function to display Message of the Day
function motd() {
    if (!isset($_SESSION['welcomed'])) {
        include('sys/var/welcome.txt');
        $_SESSION['welcomed'] = true; // Set the welcomed flag
        exit;
    }
}

// Function to get help information for commands
function getHelpInfo($command) {
    $helpInfo = include 'sys/lib/help.php';
    
    if (!empty($command)) {
        return isset($helpInfo[$command]) ? $helpInfo[$command] : "Command not found.";
    }
    $helpText = "Available commands:\n";
    foreach ($helpInfo as $cmd => $description) {
        $helpText .= "$cmd: $description\n";
    }
    return $helpText;
}