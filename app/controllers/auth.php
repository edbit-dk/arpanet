<?php

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

function auth_register($data){

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

function auth_login($data) {

    if(empty($data)) {
        return 'ERROR: Security Access Code Not Accepted!';
    } else {
        $user = validate_user_input($data);
    }

    $db_user = DB::table('users')
        ->join('levels', 'levels.id = users.level_id', 'LEFT')
        ->where('email', '=', $user['email'])
        ->where('password', '=', $user['password'])
        ->orWhere('username', '=', $user['email'])
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

function auth_user() {
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
