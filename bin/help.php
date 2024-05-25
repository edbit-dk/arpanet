<?php

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

function getVaultInfo($number) {
    $vaults = include 'sys/lib/vaults.php';
    
    if (!empty($number)) {
        return isset($vaults[$number]) ? $vaults[$number] : "Vault not found.";
    }
    $helpText = "VAULT-TEC ACTIVE VAULTS:\n";
    foreach ($vaults as $vault => $description) {
        $helpText .= " $vault: $description\n";
    }
    return $helpText;
}