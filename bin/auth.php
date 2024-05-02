<?php

// Function to handle new user creation
function newUser($data) {
    global $validCredentials;

    $node = $_SESSION['node'];

    $params = explode(' ', $data);

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "Please provide username. ";
    }

    // Check if username already exists
    if (isset($validCredentials['users'][$params[0]])) {
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
        file_put_contents("node/{$node}.json", json_encode($validCredentials));
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
    global $validCredentials;
    $params = explode(' ', $data);

    // If no parameters provided, prompt for username
    if (empty($params)) {
        return "Please provide username. ";
    }

    // If only username provided, prompt for password
    if (count($params) === 1) {
        $username = $params[0];
        // Check if username exists
        if (isset($validCredentials['users'][$username])) {
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
        // Validate password
        if (isset($validCredentials['users'][$username]) && $validCredentials['users'][$username] === $password) {
            $_SESSION['loggedIn'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['pwd'] = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $username; // Set user's directory
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
    return "Logged out successfully.";
}
