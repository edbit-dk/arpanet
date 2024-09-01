<?php

// set a constant that holds the project's folder path, like "/var/www/".
// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require ROOT . 'app/bootstrap.php';

$api_request = parse_request(Request::post('query'));

if(!empty($api_request['server'])) {
    $api_server_id = $api_request['server'];
} else {
    $api_server_id = rand(1,2);
}

$server = Server::get(Server::$id, $api_server_id);

if(!$server) {
    echo "ERROR: Connection Terminated.\n";
    return; 
}

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the command and data from POST data
    $command = strtolower($_POST['command']);
    $data = Request::post('data');

    // Execute the appropriate command
    $output = api_run($command, $data);

    // Output the result
    echo $output;
} else {
    // If accessed directly without POST method, return help information
    echo help_info('');
}


// Function to execute commands
function api_run($command, $data) {

    // MISC
    if ($command === 'motd') {
        return SystemController::motd();
    }

    if ($command === 'uplink') {
        return SystemController::uplink();
    }

    if ($command === 'help') {
        return SystemController::help();
    }

    if ($command === 'boot') {
        return SystemController::boot();
    }

    // AUTH
    if ($command === 'register') {
        return AuthController::register($data);
    }

    if ($command === 'login') {
        return AuthController::login($data);
    }

    if ($command === 'user') {
        return auth_user();
    }

    // Handle the LOGIN command separately
    if ($command === 'logon' && isset($_SESSION['USER'])) {
        return server_logon($data);
    }

    if ($command === 'version') {
        return SystemController::version();
    }

        // Check if the user is logged in
        if (!isset($_SESSION['auth']) && isset($_SESSION['USER'])) {

            switch ($command) {
                case 'set':
                    return set($data);
                case 'run':
                    return run($data);
                case 'help':
                    return help_info($data);
                case $command == 'debug' || $command == 'mem':
                    return dump($data);
                case $command == 'connect' || $command == 'telnet':
                    return ServerController::connect($data);
                case 'logoff':
                    return AuthController::logout();
                default:
                    return "ERROR: Unknown Guest Command";
            } 
            
        }


        if(isset($_SESSION['auth']) && $_SESSION['username'] != 'root') {

              switch ($command) {
                case 'accounts':
                    return listAccounts($data);
                case 'email':
                    return emailUser($data);
                case $command == 'ls' || $command == 'dir':
                    return listFiles();
                case 'cd':
                    return changeDirectory($data);
                case $command == 'cat' || $command == 'more':
                    return readFileContent($data);
                case 'logon':
                    return server_logon($data);
                case $command == 'logout' || $command == 'dc':
                    return ServerController::logout();
                case $command == 'reboot' || $command == 'autoexec' || $command == 'restart' || $command == 'start':
                    return SystemController::restart();
                case 'help':
                    return help_info($data);
                case $command == 'scan' || $command == 'find':
                    return scanNodes($data);
                case $command == 'connect' || $command == 'telnet':
                    return ServerController::connect($data);
                default:
                    return "ERROR: Unknown User Command";
              }        
          }


          if(isset($_SESSION['auth']) && $_SESSION['username'] === 'root' &&  $_SESSION['password'] === 'robco') {

             switch ($command) {
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
                    return ServerController::logout();
                 default:
                     return "ERROR: Unknown Root Command";
             }
         }

    return "
>>> UPLINK REQUIRED <<<

** IMPROPER REQUEST **
** ACCESS DENIED **";
}
