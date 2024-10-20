<?php

class ServerController
{
    public static function logon() {

    }

    public static function connect($data) {
        $server_id = explode(' ', $data)[0];

        if (file_exists(APP_CACHE . "server/{$server_id}.json")) {
            self::logout();  
            return "Contacting Server: {$server_id}\n";
        } else {
            return 'ERROR: ACCESS DENIED';
        }
    }

    public static function logout() {

        $_SESSION = array();
        session_destroy();
    
        session_start();

        Session::set(User::$session, User::session());

        $server = Session::get(Server::$session);
    
        return "LOGGING OUT FROM {$server}...\n";
    }
}

// Function to handle user login
function server_logon($data) {
    global $server, $server_id;

    if(!isset($_SESSION['DEBUG_PASS'])) {
        $_SESSION['DEBUG_PASS'] = false;
    }

    $params = explode(' ', $data);
    $max_attempts = 4; // Maximum number of allowed attempts

    // Initialize login attempts if not set
    if (!isset($_SESSION['ATTEMPTS'])) {
        $_SESSION['ATTEMPTS'] = $max_attempts;
    }

    // Check if the user is already blocked
    if (isset($_SESSION['BLOCKED']) && $_SESSION['BLOCKED'] === true) {
        return "ERROR: Terminal Locked. Please contact an administrator!";
    }

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "ERROR: Wrong Username.";
    } else {
        $username = $params[0];
    }

    // If both username and password provided, complete login process
    if (count($params) === 2) {
        $username = strtolower($params[0]);
        $password = strtolower($params[1]);

        // Validate password
        if (isset($server['accounts'][$username]) && 
        $server['accounts'][$username] == $password OR 
        $_SESSION['USER']['ID'] == 'root' && $server['admin'] == $password OR 
        $_SESSION['USER']['ID'] == 'admin' && $server['admin'] == $password OR 
        strtolower($_SESSION['DEBUG_PASS']) == $password ) {
            $_SESSION['auth'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            $_SESSION['server'] = $server_id;
            $_SESSION['home'] = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $server_id; // Set user's directory
            $_SESSION['pwd'] = HOME_DIRECTORY . DIRECTORY_SEPARATOR . $server_id; // Set user's directory
            
            $userFolder = $_SESSION['home'] . DIRECTORY_SEPARATOR . $username;
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }

            // Reset login attempts on successful login
            unset($_SESSION['ATTEMPTS']);
            unset($_SESSION['BLOCKED']);

            if (isset($_SESSION['USER'])) {
                $user_id = $_SESSION['USER']['ID'];
                $_SESSION['USER']['XP'] += 25;
                file_put_contents(APP_CACHE . "user/{$user_id}.json", json_encode($_SESSION['USER']));
            }

            return "Password Accepted.\nPlease wait while system is accessed...\n+0025 XP ";

        } else {

            $_SESSION['ATTEMPTS']--;

            // Calculate remaining attempts
            $attempts_left = $_SESSION['ATTEMPTS'];

            if ($_SESSION['ATTEMPTS'] === 1) {
                echo "WARNING: Lockout Imminent !!!\n";
            }

            // Block the user after 4 failed attempts
            if ($_SESSION['ATTEMPTS'] === 0) {
                $_SESSION['BLOCKED'] = true;
                return "ERROR: Terminal Locked. Please contact an administrator!";
            }

            return "ERROR: Wrong Username or Password.\nAttempts Remaining: {$attempts_left}";
        }
    }

    return "ERROR: Wrong Input."; // Invalid login parameters message
}