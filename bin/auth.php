<?php

// Function to handle new user creation
function newUser($data) {
    global $nodes;

    $node = $_SESSION['node'];

    $params = explode(' ', $data);

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "Please provide username. ";
    }

    // Check if username already exists
    if (isset($nodes['users'][$params[0]])) {
        unset($_SESSION['newuser']); // Clear session data
        return "Username already exists";
    }

    // If only username provided, store it in session and prompt for password
    if (count($params) === 1) {
        $username = $params[0];
        // Check if username is already set in session
        if (isset($_SESSION['newuser'])) {
            return "Enter password for $username: ";
        } else {
            // Store username in session
            $_SESSION['newuser'] = $username;
            return "Enter password for $username: ";
        }
    }

    // If both username and password provided, complete new user creation
    if (count($params) === 2) {
        $username = $_SESSION['newuser'];
        $password = $params[1];
        
        // Store the new user credentials
        $validCredentials['users'][$username] = $password;
        // Save the updated user data to the file
        file_put_contents("node/{$node}.json", json_encode($nodes));
        // Create a folder for the new user
        $userFolder = HOME_DIRECTORY . $username;
        if (!file_exists($userFolder)) {
            mkdir($userFolder, 0777, true);
        }
        unset($_SESSION['newuser']); // Clear session data
        return "User created successfully. You can logon now!";
    }

    return "Invalid new user parameters.";
}

// Function to handle user login
function loginUser($data) {
    global $nodes;

    $params = explode(' ', $data);

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "Please provide username. ";
    } else {
        $username = $params[0];
    }

    // If only username provided, prompt for password
    if (count($params) === 1 AND strpos($data, '@') !== false) {
        // Check if username exists
        if (isset($nodes['users'][$username])) {
            $_SESSION['loginUser'] = $username;
            return "Enter password for $username: ";
        } else {
            return "Invalid username.";
        }
    }

    // If both username and password provided, complete login process
    if (count($params) === 2) {
        $username = $_SESSION['loginUser'];
        $password = $params[1];

        if (strpos($data, '@') !== false) { 
            $_SESSION['node'] = explode('@', $data)[0];
    
            if (!file_exists("node/{$_SESSION['node']}.json")) {
                file_put_contents("node/{$_SESSION['node']}.json", json_encode(
                    [
                        'hostname' => $_SESSION['node'],
                        'ip' => long2ip(mt_rand()),
                        'root' => $username,
                        'users' => [$username => $password],
                        'blocked' => []
                    ]
                    ));
            } 
            
        }

        // Validate password
        if (isset($nodes['users'][$username]) && $nodes['users'][$username] === $password) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['home'] = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $username; // Set user's directory
            $_SESSION['pwd'] = HOME_DIRECTORY . DIRECTORY_SEPARATOR . $username; // Set user's directory
            
            $userFolder = HOME_DIRECTORY . $username;
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }
            
            unset($_SESSION['loginUser']); // Remove temporary session variable
            return "Welcome, $username!"; // Successful login message
        } else {
            unset($_SESSION['loginUser']); // Remove temporary session variable
            return "Invalid password."; // Invalid password message
        }
    }

    return "Invalid login parameters."; // Invalid login parameters message
}

// Function to handle whoami command
function whoAmI() {
    if (isset($_SESSION['username'])) {
        return $_SESSION['username']; // Return the logged-in user's username
    } else {
        return "You are not logged in"; // Return a message indicating not logged in
    }
}

// Function to handle user logout
function logoutUser() {
    $_SESSION = array();
    session_destroy();
    return "Disconnected successfully.";
}
