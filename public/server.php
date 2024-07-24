<?php

// set a constant that holds the project's folder path, like "/var/www/".
// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require ROOT . 'app/bootstrap.php';

$request = parse_request(Request::post('query'));

$server_id = isset($request['server']) ? $request['server'] : rand_filename(APP_CACHE . "server/");
Session::set('server', $server_id);

if(!file_exists(APP_CACHE . "server/{$server_id}.json")) {
    echo "ERROR: Connection Terminated.\n";
    return;
}

// Define valid credentials (this is just an example, in a real application, you'd use a database)
$server = json_decode(file_get_contents(APP_CACHE . "server/{$server_id}.json"), true);

// Handle POST requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the command and data from POST data
    $command = strtolower($_POST['command']);
    $data = Request::post('data');

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

    if ($command === 'register') {
        return register_user($data);
    }

    if ($command === 'login') {
        return login_user($data);
    }

    if ($command === 'motd') {
        return motd();
    }

    if ($command === 'boot') {
        return boot();
    }

    if ($command === 'user') {
        return user();
    }

    // Handle the LOGIN command separately
    if ($command === 'logon' && isset($_SESSION['USER'])) {
        return server_logon($data);
    }

    if ($command === 'version') {
        return version_info();
    }

        // Check if the user is logged in
        if (!isset($_SESSION['loggedIn']) && isset($_SESSION['USER'])) {

            switch ($command) {
                case 'set':
                    return set($data);
                case 'run':
                    return run($data);
                case 'help':
                    return getHelpInfo($data);
                case $command == 'debug' || $command == 'mem':
                    return dump($data);
                case $command == 'connect' || $command == 'telnet':
                    return contact_server($data);
                case 'logoff':
                    return disconnect_network();
                default:
                    return "ERROR: Unknown Guest Command";
            } 
            
        }


        if(isset($_SESSION['loggedIn']) && $_SESSION['username'] != 'root') {

            logMessage(strtoupper($_SESSION['username']) . ' used command: ' . $command . " {$data}", $server_id);
        
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
                    return logout_user();
                case $command == 'reboot' || $command == 'autoexec' || $command == 'restart' || $command == 'start':
                    return restartServer();
                case 'help':
                    return getHelpInfo($data);
                case $command == 'scan' || $command == 'find':
                    return scanNodes($data);
                case $command == 'connect' || $command == 'telnet':
                    return contact_server($data);
                default:
                    return "ERROR: Unknown User Command";
              }        
          }


          if(isset($_SESSION['loggedIn']) && $_SESSION['username'] === 'root' &&  $_SESSION['password'] === 'robco') {

            logMessage(strtoupper($_SESSION['username']) . ' used command: ' . $command . " {$data}", $server_id);
        
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
                    return logout_user();
                 default:
                     return "ERROR: Unknown Root Command";
             }
         }

    return "ERROR: Unknown Command";
}
