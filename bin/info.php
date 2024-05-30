<?php

function getVersionInfo() {
    return file_get_contents('sys/var/version.txt');
}

// Function to get help information for commands
function getHelpInfo($command) {
    $helpInfo = include 'sys/lib/help.php';

    $command = strtoupper($command);
    
    if (!empty($command)) {
        return isset($helpInfo[$command]) ? $helpInfo[$command] : "Command not found.";
    }
    $helpText = "HELP:\n";
    foreach ($helpInfo as $cmd => $description) {
        $helpText .= " $cmd $description\n";
    }
    return $helpText;
}

function getTerminalInfo($number) {
    $terminals = include 'sys/lib/terminals.php';
    
    if (!empty($number)) {
        return isset($terminals[$number]) ? $terminals[$number] : "Terminal not found.";
    }
    $helpText = "Searching PoseidoNet Comlinks Stations...\n";

    foreach ($terminals as $terminal => $description) {
        $helpText .= " $terminal: $description\n";
    }
    return $helpText;
}

