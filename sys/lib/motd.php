<?php

global $server_id, $server;

$server_name = $server['name'];
$location = $server['location'];

if(!isset($_SESSION['username']) && $server_id === 0) {
    echo <<< EOT
Welcome to ROBCO Industries (TM) Termlink
-Server {$server_id}-
 
Uplink with central PoseidoNet initiated...
 
Security Access Code Sequence Required: 
 
70Y644
008Z21
9X7299
A46123
 
Security Access Code Sequence Accepted.
 
Welcome to PoseidoNET!
EOT;

return;
}

if(isset($_SESSION['username'])) {
    
    $username = $_SESSION['username'];

    if(isset($server['accounts'][$username])
    && $server['accounts'][$username] === $_SESSION['password'] 
    OR $server['root'] === $_SESSION['password'] ) {

    $username = strtoupper($username);

    echo <<< EOT
    ROBCO INDUSTRIES UNIFIED OPERATING SYSTEM
    COPYRIGHT 2075-2077 ROBCO INDUSTRIES
    -Server {$server_id} ({$location})-
    
    $server_name
       
    Welcome, {$username}.
    _________________________________________
    EOT;
    return;
    }

} else {

echo <<< EOT
Welcome to ROBCO Industries (TM) Termlink
-Server {$server_id}-
 
Password Required
EOT;

}
