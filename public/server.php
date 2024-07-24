<?php

// set a constant that holds the project's folder path, like "/var/www/".
// DIRECTORY_SEPARATOR adds a slash to the end of the path
define('ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// set a constant that holds the project's "application" folder, like "/var/www/application".
define('APP', ROOT . 'app' . DIRECTORY_SEPARATOR);
define('APP_CONTROLLER', APP . 'controllers' . DIRECTORY_SEPARATOR);
define('APP_MODEL', APP . 'models' . DIRECTORY_SEPARATOR);
define('APP_STORAGE', APP . 'storage' . DIRECTORY_SEPARATOR);
define('APP_CACHE', APP_STORAGE . 'cache' . DIRECTORY_SEPARATOR);

// auto-loading the classes (currently only from application/libs) via Composer's PSR-4 auto-loader
// later it might be useful to use a namespace here, but for now let's keep it as simple as possible
require ROOT . 'vendor/autoload.php';

// load application config (error reporting etc.)
require APP . 'config/app.php';

#DB::connect(DB_HOST, DB_NAME, DB_USER, DB_PASS);

session_start(); // Start the session

// Define the home directory
define('HOME_DIRECTORY', ROOT . "/public/uploads/");

define('DEFAULT_NODE', '0');

$special_chars = "!?,;.'[]={}@#$%^*()-_\/|";

require_once APP_CONTROLLER . 'system.php';
require_once APP_CONTROLLER . 'debug.php';
require_once APP_CONTROLLER . 'filesystem.php';
require_once APP_CONTROLLER . 'auth.php';
require_once APP_CONTROLLER . 'info.php';
require_once APP . 'helpers.php';

$request = parse_get('query');
$server_id = isset($request['server']) ? $request['server'] : rand_filename(APP_CACHE . "server/");

if(!file_exists(APP_CACHE . "server/{$server_id}.json")) {
    echo "ERROR: Connection Terminated.\n";
    return;
}

if(isset($request['server']) OR !isset($_SESSION['server'])) {
   $_SESSION['server'] = $server_id;
} else {
    $server_id = $_SESSION['server'];
}


// Define valid credentials (this is just an example, in a real application, you'd use a database)
$server = json_decode(file_get_contents(APP_CACHE . "server/{$server_id}.json"), true);

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

    if ($command === 'enter') {
        return connectUser($data);
    }

    if ($command === 'motd') {
        return motd();
    }

    if ($command === 'boot') {
        return boot();
    }

    if ($command === 'user') {
        return connectUser($data);
    }

    // Handle the LOGIN command separately
    if ($command === 'logon' && isset($_SESSION['USER'])) {
        return loginUser($data);
    }

    if ($command === 'version') {
        return getVersionInfo();
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
                    return connectServer($data);
                case 'logoff':
                    return disconnectUser();
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
                    return loginUser($data);
                case $command == 'logout' || $command == 'dc':
                    return logoutUser();
                case $command == 'reboot' || $command == 'autoexec' || $command == 'restart' || $command == 'start':
                    return restartServer();
                case 'help':
                    return getHelpInfo($data);
                case $command == 'scan' || $command == 'find':
                    return scanNodes($data);
                case $command == 'connect' || $command == 'telnet':
                    return connectServer($data);
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
                    return logoutUser();
                 default:
                     return "ERROR: Unknown Root Command";
             }
         }

    return "ERROR: Unknown Command";
}
