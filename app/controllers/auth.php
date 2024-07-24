<?php

require APP_MODEL . 'user.php';
require APP_MODEL . 'server.php';
require APP_MODEL . 'log.php';

function validate_user_input($data) {

    $input = explode(' ', trim($data));

    if (count($input) >= 1 && strlen($input[0]) === 27 && preg_match('/^[AXYZ01234679-]+$/', $input[0])) {

        $user['password'] = $input[0];
        $user['email'] = $input[1];

    } else {
        return 'ERROR: Security Access Code Not Accepted!';
    }

    return $user;
}

function register_user($data){

    if(empty($data)) {
        return 'ERROR: Security Access Code Not Accepted!';
    } else {
        $user = validate_user_input($data);
        $user['firstname'] = ucfirst(strtolower(wordlist(APP_STORAGE . 'text/namelist.txt', rand(5, 12) , 1)[0]));
        $user['lastname'] = ucfirst(strtolower(wordlist(APP_STORAGE . 'text/namelist.txt', rand(5, 12) , 1)[0]));
    }

    $user_exists = DB::table('users')
    ->select(['email'])
    ->where('email', '=', $user['email'])
    ->limit(1)
    ->read();

    if(empty($user_exists)) {

        $password = $user['password'];
        $firstname = $user['firstname'];
        $lastname = $user['lastname'];
        $fullname = "$firstname $lastname";

        $user_id = DB::table('users')->insert([
            'email' =>  $user['email'],
            'password' => $password,
            'firstname' => $firstname,
            'lastname' =>  $lastname,
            'fullname' => $fullname,
            'last_login' => date(TIMESTAMP_FORMAT),
            'created_at' => date(TIMESTAMP_FORMAT)
        ]);

        $username = 'PE-' . strtoupper(random_username($firstname, $user_id));

        DB::table('users')
        ->where('id', '=', $user_id)
        ->update(['username' => $username]);

        $_SESSION['USER'] = [
            'EMPLOYEE ID' => $username,
            'NAME' => $fullname,
            'LEVEL' => 'UNKNOWN',
            'XP' => 0
        ];

        sleep(1);

        return "ACCESS CODE: {$password}\nEMPLOYEE ID: {$username}\n";

    } else {
        return 'ERROR: Employee already exists!';
    }

}

function login_user($data) {

    if(empty($data)) {
        return 'ERROR: Security Access Code Not Accepted!';
    } else {
        $user = validate_user_input($data);
    }

    $db_user = DB::table('users')
        ->join('levels', 'levels.id = users.level_id', 'LEFT')
        ->where('email', '=', $user['email'])
        ->orWhere('username', '=', $user['email'])
        ->where('password', '=', $user['password'])
        ->first();

    if(!empty($db_user)) { 

        $_SESSION['USER'] = [
            'EMPLOYEE ID' => $db_user['username'],
            'NAME' => $db_user['fullname'],
            'LEVEL' => $db_user['rep'],
            'XP' => $db_user['xp']
        ];

        $password = $db_user['password'];
        $username = $db_user['username'];

        sleep(1);

        return "ACCESS CODE: {$password}\nEMPLOYEE ID: {$username}\n";

    } else {
        return 'ERROR: Employee not found!'; 
    }

}

function user() {
    if (isset($_SESSION['USER'])) {
        foreach($_SESSION['USER'] as $user => $data) {
            echo "{$user}: {$data} \n";
        };
        return;
    }
}

function contact_server($data) {

    $server_id = explode(' ', $data)[0];

    if (file_exists(APP_CACHE . "server/{$server_id}.json")) {
        logout_user();  
        return "Contacting Server: {$server_id}\n";
    } else {
        return 'ERROR: ACCESS DENIED';
    }

}

function setupServer($data) {

    $server_id = explode(' ', $data)[0];

    $username = $_SESSION['username'];
    $password = $_SESSION['password'];
    
    if (!file_exists(APP_CACHE . "server/{$server_id}.json")) {
        file_put_contents(APP_CACHE. "server/{$server_id}.json", json_encode(
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
function createUser($data) {
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
        file_put_contents(APP . "server/{$_SESSION['server_id']}.json", json_encode($server));

        // Create a folder for the new user
        $userFolder  = realpath(HOME_DIRECTORY) . DIRECTORY_SEPARATOR . $server_id; // Set user's directory
            
            if (!file_exists($userFolder)) {
                mkdir($userFolder, 0777, true);
            }

        unset($_SESSION['newuser']); // Clear session data
        return "Welcome new user, {$username}";
    }

    return "ERROR: NEW_USER";
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
            $_SESSION['loggedIn'] = true;
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

            logMessage(strtoupper($_SESSION['username']) . ' logged in.', $server_id);
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


// Function to handle whoami command
function whoAmI() {
    global $server_id;

    if (isset($_SESSION['username'])) {
        return $_SESSION['username']; // Return the logged-in user's username
    } else {
        return "ERROR: Logon Required."; // Return a message indicating not logged in
    }
}

// Function to handle user logout
function logout_user() {

    global $server_id;

    $user = $_SESSION['USER'];

    $_SESSION = array();
    session_destroy();

    session_start();

    $_SESSION['USER'] = $user;

    return "LOGGING OUT FROM {$server_id}...\n";
}


function disconnect_network() {
    $_SESSION = array();
    session_destroy();

    return "DISCONNECTING from PoseidoNET...\n";
}
