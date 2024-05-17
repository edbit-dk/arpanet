<?php

// Function to handle new user creation
function newUser($data) {
    global $node;

    $params = explode(' ', $data);

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "ERROR: USERNAME REQUIRED";
    }

    // Check if username already exists
    if (isset($node['accounts'][$params[0]])) {
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
        $node['accounts'][$username] = $password;
        // Save the updated user data to the file
        file_put_contents("node/{$_SESSION['node']}.json", json_encode($node));
        // Create a folder for the new user
        $userFolder = HOME_DIRECTORY . $username;
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
    global $node;

    $params = explode(' ', $data);

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "ERROR: WRONG USERNAME";
    } else {
        $username = $params[0];
    }

    // If only username provided, prompt for password
    if (count($params) === 1) {

        // Check if username exists
        if (isset($node['accounts'][$username])) {
            $_SESSION['loginUser'] = $username;
            return "ENTER PASSWORD:";
        } else {
            return "ERROR: WRONG USERNAME";
        }
    }
    

    // If both username and password provided, complete login process
    if (count($params) === 2) {
        $username = $_SESSION['loginUser'];
        $password = $params[1];

        if(strpos($data, '@') !== false) {
            $_SESSION['node'] = explode('@', $data)[0];
    
            if (!file_exists("node/{$_SESSION['node']}.json")) {
                file_put_contents("node/{$_SESSION['node']}.json", json_encode(
                    [
                        'hostname' => $_SESSION['node'],
                        'ip' => long2ip(mt_rand()),
                        'root' => $username,
                        'accounts' => [$username => $password],
                        'blocked' => []
                    ]
                    ));
            } 
    
        }

        // Validate password
        if (isset($node['accounts'][$username]) && $node['accounts'][$username] === $password) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['home'] = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $username; // Set user's directory
            $_SESSION['pwd'] = HOME_DIRECTORY . DIRECTORY_SEPARATOR . $username; // Set user's directory
            
            $userFolder = HOME_DIRECTORY . $username;
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }
            
            unset($_SESSION['loginUser']); // Remove temporary session variable
            include('sys/var/welcome.txt');
            return "\nWelcome, $username!"; // Successful login message
        } else {
            unset($_SESSION['loginUser']); // Remove temporary session variable
            return "ERROR: WRONG PASSWORD"; // Invalid password message
        }
    }

    return "ERROR: WRONG INPUT"; // Invalid login parameters message
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
    return "LOGGING OUT...";
}
