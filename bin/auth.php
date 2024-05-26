<?php

function connectServer($data) {

    $server_id = explode(' ', $data)[0];

    if(!isset($_SESSION['loggedIn'])) { 
        if (file_exists("server/{$server_id}.json")) { 
            return "Connecting to server: {$server_id}...";
        } else {
            return 'ERROR: Connection refused.';
        }
    }

    if(isset($_SESSION['loggedIn'])) {

        $username = $_SESSION['username'];

        if (file_exists("home/{$server_id}/{$username}")) {
            $_SESSION['home'] = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $server_id . DIRECTORY_SEPARATOR . $username; // Set user's directory
            $_SESSION['pwd'] = HOME_DIRECTORY . DIRECTORY_SEPARATOR . $server_id . DIRECTORY_SEPARATOR . $username; // Set user's directory
            return "Connecting to server: {$server_id}...";
        } else {
            return 'ERROR: Connection terminated.';
        }
    }

    


}

function setupServer($data) {

    $server_id = explode(' ', $data)[0];

    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    
    if (!file_exists("server/{$server_id}.json")) {
        file_put_contents("server/{$server_id}.json", json_encode(
        [
                'name' => 'Default',
                'server' => $_SESSION['server_id'],
                'ip' => long2ip(mt_rand()),
                'root' => $username,
                'accounts' => [$username => $password],
                'blocked' => []
        ]
    ));
    } 
}

// Function to handle new user creation
function newUser($data) {
    global $server, $server_id;

    $params = explode(' ', $data);

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "ERROR: USERNAME REQUIRED";
    }

    // Check if username already exists
    if (isset($server['accounts'][$params[0]])) {
        unset($_SESSION['newuser']); // Clear session data
        return "ERROR: USERNAME IN USE";
    }

    // If only username provided, store it in session and prompt for password
    if (count($params) === 1) {
        $username = $params[0];
        // Check if username is already set in session
        if (isset($_SESSION['newuser'])) {
            return "ENTER PASSWORD NOW: $username: ";
        } else {
            // Store username in session
            $_SESSION['newuser'] = $username;
            return "ENTER PASSWORD NOW: $username: ";
        }
    }

    // If both username and password provided, complete new user creation
    if (count($params) === 2) {
        $username = $_SESSION['newuser'];
        $password = $params[1];
        
        // Store the new user credentials
        $server['accounts'][$username] = $password;
        // Save the updated user data to the file
        file_put_contents("server/{$_SESSION['server_id']}.json", json_encode($server));

        // Create a folder for the new user
        $userFolder  = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $server_id . DIRECTORY_SEPARATOR . $username; // Set user's directory
            
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }

        unset($_SESSION['newuser']); // Clear session data
        return "Welcome new user, {$username}";
    }

    return "ERROR: NEW_USER";
}

// Function to handle user login
function loginUser($data) {
    global $server, $server_id;

    $params = explode(' ', $data);
    $max_attempts = 4; // Maximum number of allowed attempts

    // Initialize login attempts if not set
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }

    // Check if the user is already blocked
    if (isset($_SESSION['blocked']) && $_SESSION['blocked'] === true) {
        return "ERROR: Terminal Locked. Please contact an administrator!";
    }

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "ERROR: Wrong Username.";
    } else {
        $username = $params[0];
    }

    // Initialize attempts for this user if not set
    if (!isset($_SESSION['login_attempts'][$username])) {
        $_SESSION['login_attempts'][$username] = 0;
    }

    if (!isset($server['accounts'][$username])) {
        return "ERROR: Wrong Username.";
    }

    // If both username and password provided, complete login process
    if (count($params) === 2) {
        $username = $params[0];
        $password = $params[1];

        // Validate password
        if (isset($server['accounts'][$username]) && $server['accounts'][$username] === $password 
        OR $server['pass'] === $password ) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['password'] = $password;
            $_SESSION['home'] = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $server_id . DIRECTORY_SEPARATOR . $username; // Set user's directory
            $_SESSION['pwd'] = HOME_DIRECTORY . DIRECTORY_SEPARATOR . $server_id . DIRECTORY_SEPARATOR . $username; // Set user's directory
            
            $userFolder = $_SESSION['home'];
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }

            if (!isset($_SESSION['server_id'])) {
                $_SESSION['server_id'] = $server_id;
            }

            // Reset login attempts on successful login
            unset($_SESSION['login_attempts'][$username]);
            unset($_SESSION['blocked']);
            echo "EXCACT MATCH!\n";
            return "Password Accepted. Please wait while system is accessed.";

        } else {
            $_SESSION['login_attempts'][$username] += 1;

            // Calculate remaining attempts
            $attempts_left = $max_attempts - $_SESSION['login_attempts'][$username];

            if ($_SESSION['login_attempts'][$username] === 3) {
                echo "WARNING: Lockout Imminent !!!\n";
            }

            // Block the user after 4 failed attempts
            if ($_SESSION['login_attempts'][$username] >= 4) {
                $_SESSION['blocked'] = true;
                $server['blocked'][$username] = 1;
                return "ERROR: Terminal Locked. Please contact an administrator!";
            }

            return "ERROR: Wrong Password. Attempts Remaining: {$attempts_left}";
        }
    }

    return "ERROR: Wrong Input."; // Invalid login parameters message
}


// Function to handle whoami command
function whoAmI() {
    if (isset($_SESSION['username'])) {
        return $_SESSION['username']; // Return the logged-in user's username
    } else {
        return "ERROR: LOGON REQUIRED"; // Return a message indicating not logged in
    }
}

// Function to handle user logout
function logoutUser() {
    $_SESSION = array();
    session_destroy();

return 'LOGGING OUT...';
}

// Function to handle user logout
function restartServer() {
    $_SESSION = array();
    session_destroy();
    return "RESTARTING...";
}