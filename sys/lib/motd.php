<?php

global $server_id, $server;

$server_name = $server['name'];

if(isset($_SESSION['username'])) {

$username = strtoupper($_SESSION['username']);

echo <<< EOT
ROBCO INDUSTRIES UNIFIED OPERATING SYSTEM
COPYRIGHT 2075-2077 ROBCO INDUSTRIES
-Server {$server_id}-             
     
Welcome, {$username}
_________________________________________
[ > $server_name]
EOT;

} else {

echo <<< EOT
Welcome to ROBCO Industries (TM) Termlink
-Server {$server_id}-
 
System Online
_________________________________________
Password Required
EOT;
}
