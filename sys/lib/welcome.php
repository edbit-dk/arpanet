<?php

global $server_id, $server;

$server_name = $server['name'];

if(isset($_SESSION['username'])) {

$username = $_SESSION['username'];

echo <<< EOT
DATANET UNIFIED OPERATING SYSTEM
COPYRIGHT 1969-1977 DATANET   
-Server {$server_id} ($server_name)-             
     
Welcome, {$username}
_________________________________
EOT;

} else {

echo <<< EOT
DATANET UNIFIED OPERATING SYSTEM
COPYRIGHT 1969-1977 DATANET   
-Server {$server_id} ($server_name)-        
     
WELCOME TO DATANET TERMLINK
_________________________________  
EOT;
}
