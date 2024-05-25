<?php

global $server_id, $server;

$server_name = $server['name'];

if(isset($_SESSION['username'])) {

$username = $_SESSION['username'];

echo <<< EOT
ROBCO INDUSTRIES UNIFIED OPERATING SYSTEM
COPYRIGHT 2075-2077 ROBCO INDUSTRIES
-Vault {$server_id} ($server_name)-             
     
Welcome, {$username}
_________________________________________
EOT;

} else {

echo <<< EOT
Welcome to ROBCO Industries (TM) Termlink
-Server {$server_id} ($server_name)-
_________________________________________  
EOT;
}
