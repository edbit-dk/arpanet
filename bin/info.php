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

function scanNodes($number) {
    global $server;

    $nodes = $server['nodes'];
    
    if (!empty($number)) {
        return isset($nodes[$number]) ? $nodes[$number] : "Terminal not found.";
    }
    $terminal = "Searching PoseidoNet for Comlinks Nodes...\n";

    foreach ($nodes as $node => $description) {
        $terminal .= " $node: $description\n";
    }
    return $terminal;
}

