<?php

$access_code = random_str(6, 'AXYZ01234679');

if(!isset($_SESSION['USER']) && !isset($_SESSION['loggedIn'])) {
    echo <<< EOT
    Welcome to ROBCO Industries (TM) Termlink

    > Uplink with central PoseidoNet initiated...
    
    Security Access Code Required...

    Enter Security Access Code: USER {$access_code} [USERNAME].
    
    EOT;

    return;
}

global $server_id, $server;

$server_name = $server['name'];
$location = $server['location'];
$status = $server['status'];

if(!isset($_SESSION['loggedIn'])) {
    echo <<< EOT
    Welcome to ROBCO Industries (TM) Termlink
    -Server {$server_id}-
     
    Password Required
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

