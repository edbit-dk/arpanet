<?php

global $server_id, $server;

$server_name = $server['name'];
$location = $server['location'];

if(isset($_SESSION['username'])) {
    
    $username = $_SESSION['username'];

    if(isset($server['accounts'][$username])
    && $server['accounts'][$username] === $_SESSION['password'] 
    OR $server['pass'] === $_SESSION['password'] ) {

    $username = strtoupper($username);

    echo <<< EOT
    ROBCO INDUSTRIES UNIFIED OPERATING SYSTEM
    COPYRIGHT 2075-2077 ROBCO INDUSTRIES
    -Vault {$server_id} ({$location})-
   
        
    Welcome, {$username}
    _________________________________________
    [ > $server_name]
    EOT;

    }

} else {

echo <<< EOT
Welcome to ROBCO Industries (TM) Termlink
-Server {$server_id}-
 
System Online
_________________________________________
Password Required
EOT;
}
