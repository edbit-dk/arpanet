<?php

global $server_id, $server;

$server_name = $server['name'];
$location = $server['location'];
$status = $server['status'];

if(!isset($_SESSION['loggedIn']) && $server_id == 0) {
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

if(isset($_SESSION['loggedIn'])) {
    
    $username = strtoupper($_SESSION['username']);

    echo <<< EOT
    ROBCO INDUSTRIES UNIFIED OPERATING SYSTEM
    COPYRIGHT 2075-2077 ROBCO INDUSTRIES
    -Server {$server_id} ({$status})-
    
    {$server_name}
    [{$location}]
       
    Welcome, {$username}.
    _________________________________________
    EOT;
    return;

}

echo <<< EOT
Welcome to ROBCO Industries (TM) Termlink
-Server {$server_id}-
 
Password Required
EOT;
