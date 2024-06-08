<?php

global $server_id, $server;

if(!isset($_SESSION['USER']) && !isset($_SESSION['loggedIn'])) {

    $code_1 = random_str(6, 'AXYZ01234679');
    $code_2 = random_str(6, 'AXYZ01234679');
    $code_3 = random_str(6, 'AXYZ01234679');

    $access_code = "{$code_1}-{$code_2}-{$code_3}"; 

    echo <<< EOT
    
    Welcome to POSEIDON ENERGY Corporation
    -Begin your Odyssey with us-

    This terminal allows access to PoseidoNET.
    __________________________________________

    > Uplink with central PoseidoNet initiated...

    #################################
    # Security Access Code Sequence #
    #################################
    
    > Enter: CODE {$access_code} [EMPLOYEE ID].

    EOT;

    return;
}

if(isset($_SESSION['loggedIn']) && isset($_SESSION['USER'])) {
    
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

return "ERROR: Unknown Guest Command";