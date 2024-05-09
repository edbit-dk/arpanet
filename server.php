<?php
session_start(); // Start the session

// Define the home directory
define('HOME_DIRECTORY', getcwd() . "/home/");

define('DEFAULT_NODE', 'guest');

$special_chars = "!?,;.'[]={}@#$%^*()-_\/|";

require_once 'bin/help.php';
require_once 'bin/debug.php';
require_once 'bin/filesystem.php';
require_once 'bin/auth.php';

if(!isset($_SESSION['node'])) {
    $_SESSION['node'] = DEFAULT_NODE;
}

// Define valid credentials (this is just an example, in a real application, you'd use a database)
$node = json_decode(file_get_contents("node/{$_SESSION['node']}.json"), true);

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the command and data from POST data
    $command = $_POST['command'];
    $data = $_POST['data'];

    // Execute the appropriate command
    $output = executeCommand($command, $data);

    // Output the result
    echo $output;
} else {
    // If accessed directly without POST method, return help information
    echo getHelpInfo('');
}

// Function to execute commands
function executeCommand($command, $data) {

    global $node;

    $passwords = array_values($node['users']);
    $usernames = array_keys($node['users']);

    $users = array_merge($passwords, $usernames);

    // Check if the user is logged in
    if (!isset($_SESSION['loggedIn']) && $command !== 'connect' && $command !== 'welcome' && $command !== 'newuser') {
        return "You must be logged in to execute commands.";
    }

    // Handle the LOGON command separately
    if ($command === 'connect') {
        return loginUser($data);
    }

    switch ($command) {
        case 'welcome':
            return motd();
        case 'newuser':
            return newUser($data);
        case 'ls':
            return listFiles();
        case 'mkdir':
            return createFolder($data);
        case 'echo': // Handle echo command here
            return echoToFile($data);
        case 'cd':
            return changeDirectory($data);
        case 'mv':
            return moveFileOrFolder($data);
        case 'cat':
            return readFileContent($data);
        case 'rm':
            return deleteFileOrFolder($data);
        case 'connect':
            return loginUser($data);
        case 'dc':
            return logoutUser();
        case 'help':
            return getHelpInfo($data);
        case 'whoami':
            return whoAmI();
        case 'debug':
            return dump($users);
        default:
            return "Command not supported.";
    }
}
