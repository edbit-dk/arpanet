<?php


session_start(); // Start the session

// Define the home directory
define('HOME_DIRECTORY', getcwd() . "/home/");

define('DEFAULT_NODE', '0');

$special_chars = "!?,;.'[]={}@#$%^*()-_\/|";

require_once 'bin/system.php';
require_once 'bin/debug.php';
require_once 'bin/filesystem.php';
require_once 'bin/auth.php';
require_once 'bin/info.php';
require_once 'bin/helpers.php';

$request = parse_get('query');

$server_id = isset($request['server']) ? $request['server'] : 1;

if (!file_exists("server/{$server_id}.json")) { 
    $server_id = DEFAULT_NODE;
}


// Define valid credentials (this is just an example, in a real application, you'd use a database)
$server = json_decode(file_get_contents("server/{$server_id}.json"), true);

if(isset($_SESSION['loggedIn']) && $_SESSION['server'] != $server_id && $_SESSION['username'] != 'overseer') {

    echo "ERROR: Connection Terminated.\n";
    return;
}

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

    global $server_id;

    if ($command === 'user') {
        return connectUser($data);
    }

    if ($command === 'score') {
        return listUsers();
    }

    // Handle the LOGIN command separately
    if ($command === 'logon') {
        return loginUser($data);
    }

    if ($command === 'version') {
        return getVersionInfo();
    }

        // Check if the user is logged in
        if (!isset($_SESSION['loggedIn'])) {

            switch ($command) {
                case 'set':
                    return set($data);
                case 'run':
                    return run($data);
                case 'boot':
                    return boot();
                case 'motd':
                    return motd();
                case 'help':
                    return getHelpInfo($data);
                case $command == 'debug' || $command == 'mem':
                    return dump($data);
                case 'connect':
                    return connectServer($data);
                case 'logoff':
                    return disconnectUser();
                default:
                    return "ERROR: Unknown Guest Command";
            } 
            
        }

        if(isset($_SESSION['loggedIn']) && $_SESSION['username'] != 'root') {

            //  logMessage($_SESSION['username'] . ' used command: ' . $command . " {$data}", $server_id);
        
              switch ($command) {
                case 'motd':
                    return motd();
                case 'email':
                    return emailUser($data);
                case $command == 'ls' || $command == 'dir':
                    return listFiles();
                case 'cd':
                    return changeDirectory($data);
                case $command == 'cat' || $command == 'more':
                    return readFileContent($data);
                case 'logon':
                    return loginUser($data);
                case $command == 'logout' || $command == 'dc':
                    return logoutUser();
                case $command == 'reboot' || $command == 'autoexec' || $command == 'restart' || $command == 'start':
                    return restartServer();
                case 'help':
                    return getHelpInfo($data);
                case $command == 'scan' || $command == 'find':
                    return scanNodes($data);
                case 'connect':
                    return connectServer($data);
                default:
                    return "ERROR: Unknown User Command";
              }        
          }

          if(isset($_SESSION['loggedIn']) && $_SESSION['username'] === 'root' &&  $_SESSION['password'] === 'robco') {

            // logMessage($_SESSION['username'] . ' used command: ' . $command);
        
             switch ($command) {
                case 'motd':
                    return motd();
                case $command == 'ls' || $command == 'dir':
                    return listFiles();
                case 'cd':
                    return changeDirectory($data);
                case $command == 'cat' || $command == 'more':
                    return readFileContent($data);
                 case $command == 'echo' || $command == 'edit': // Handle echo command here
                     return echoToFile($data);
                 case $command == 'mv' || $command == 'move':
                     return moveFileOrFolder($data);
                 case 'mkdir':
                     return createFolder($data);
                 case $command == 'rm' || $command == 'del':
                     return deleteFileOrFolder($data);
                case $command == 'logout' || $command == 'dc':
                    return logoutUser();
                 default:
                     return "ERROR: Unknown Root Command";
             }
         }

}