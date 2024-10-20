<?php

class AuthController 
{

    public static function validate($data) {

        $input = explode(' ', trim($data));

        if (count($input) >= 1 && strlen($input[0]) === 27 && preg_match('/^[AXYZ01234679-]+$/', $input[0])) {

            $user[User::$password] = $input[0];
            $user[User::$email] = $input[1];

        } else {
            return Text::get('ERROR_ACCESS_CODE');
        }

        return $user;
    }

    public static function register($data) {
        if(empty($data)) {
            return Text::get('ERROR_ACCESS_CODE');
        } else {
            $user = self::validate($data);
            $user[User::$firstname] = ucfirst(strtolower(wordlist(APP_STORAGE . 'text/namelist.txt', rand(5, 12) , 1)[0]));
            $user[User::$lastname] = ucfirst(strtolower(wordlist(APP_STORAGE . 'text/namelist.txt', rand(5, 12) , 1)[0]));
        }
    
        $user_exists = User::get(User::$email, $user[User::$email]);
    
        if(empty($user_exists)) {
    
            $password = $user[User::$password];
            $firstname = $user[User::$firstname];
            $lastname = $user[User::$lastname];
            $fullname = "$firstname $lastname";
    
            $user_id = User::create([
                User::$email =>  $user[User::$email],
                User::$password => $password,
                User::$firstname => $firstname,
                User::$lastname =>  $lastname,
                User::$fullname => $fullname,
                User::$created_at => date(TIMESTAMP_FORMAT)
            ]);
    
            $username = 'PE-' . strtoupper(random_username($firstname, $user_id));
    
            User::update(User::$id . ",=,{$user_id}", [User::$username => $username]);
    
            $_SESSION[User::$session] = [
                'EMPLOYEE ID' => $username,
                'NAME' => $fullname,
                'LEVEL' => 'UNKNOWN',
                'XP' => 0
            ];
    
            sleep(1);
    
            return "ACCESS CODE: {$password}\nEMPLOYEE ID: {$username}\n";
    
        } else {
            return Text::get('ERROR_USER_TAKEN');
        }
    }

    public static function login($data) {
        if(empty($data)) {
            return Text::get('ERROR_ACCESS_CODE');
        } else {
            $user = self::validate($data);
        }
    
        $db_user = User::auth($user);

        var_dump($db_user);
        die;
    
        if(!empty($db_user)) { 
    
            Session::set(User::$session, [
                'EMPLOYEE ID' => $db_user[User::$username],
                'NAME' => $db_user[User::$fullname],
                'LEVEL' => $db_user[User::$rep],
                'XP' => $db_user[User::$xp]
            ]);
    
            $password = $db_user[User::$password];
            $username = $db_user[User::$username];
    
            sleep(1);
    
            return "ACCESS CODE: {$password}\nEMPLOYEE ID: {$username}\n";
    
        } else {
            return Text::get('ERROR_USER_404');
        }
    }

    public static function logout() {
        $_SESSION = array();
        session_destroy();
    
        return "DISCONNECTING from PoseidoNET...\n";
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
