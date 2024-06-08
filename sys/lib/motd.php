<?php

global $server_id, $server;

if(!isset($_SESSION['USER']) && !isset($_SESSION['loggedIn'])) {

    $code_1 = random_str(6, 'AXYZ01234679');
    $code_2 = random_str(6, 'AXYZ01234679');
    $code_3 = random_str(6, 'AXYZ01234679');

    $access_code = "{$code_1}-{$code_2}-{$code_3}"; 

    $employee_id = strtoupper(random_username(wordlist('sys/var/wordlist.txt', rand(5, 10) , 1)[0]));

    $_SESSION['EMPLOYEE_ID'] = strtolower($employee_id);

    echo <<< EOT
    
    Welcome to POSEIDON ENERGY Corporation
    -Begin your Odyssey with us-

    This terminal allows access to PoseidoNET.
    Type HELP after logon for more commands.
    __________________________________________

    Uplink with central PoseidoNet initiated.

    Enter Security Access Code Sequence:

    #################################
    ACCESS CODE: {$access_code}
    EMPLOYEE ID: {$employee_id}
    #################################
    
    Remember <ACCESS CODE> And <EMPLOYEE ID> !

    > ENTER <ACCESS CODE> [EMPLOYEE ID]:
     
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

return "ERROR: Unknown Guest Command";