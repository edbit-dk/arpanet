<?php
session_start(); // Start the session

// Define the home directory
define('HOME_DIRECTORY', getcwd() . "/home/");

define('DEFAULT_NODE', '1');

$special_chars = "!?,;.'[]={}@#$%^*()-_\/|";

require_once 'bin/debug.php';
require_once 'bin/filesystem.php';
require_once 'bin/auth.php';
require_once 'bin/help.php';
require_once 'bin/url.php';

$request = parse_get('query');

$server_id = isset($request['server']) ? $request['server'] : 1;

if (!file_exists("server/{$server_id}.json")) { 
    $server_id = 1;
}

// Define valid credentials (this is just an example, in a real application, you'd use a database)
$server = json_decode(file_get_contents("server/{$server_id}.json"), true);

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the command and data from POST data
    $command = strtolower($_POST['command']);
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

    // Check if the user is logged in
    if (!isset($_SESSION['loggedIn']) 
    && $command !== 'logon' 
    && $command !== 'boot' 
    && $command !== 'register'
    && $command !== 'connect'  
    && $command !== 'help'
    && $command !== 'debug') {
        return "Password Required";
    }

    // Handle the LOGIN command separately
    if ($command === 'logon') {
        return loginUser($data);
    }

    switch ($command) {
        case 'boot':
            return motd();
        case 'register':
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
        case 'logon':
            return loginUser($data);
        case 'logout':
            return logoutUser();
        case 'help':
            return getHelpInfo($data);
        case 'whoami':
            return whoAmI();
        case 'debug':
            return dump($data);
        case 'connect':
            return connectServer($data);
        default:
            return "ERROR: UNKNOWN COMMAND";
    }
}
