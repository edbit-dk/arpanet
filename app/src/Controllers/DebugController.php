<?php

namespace App\Controllers;

use App\Services\Controller;

use App\Models\User;

class DebugController extends Controller
{

    public function set($request, $response) 
    {
        $data = $request->getParam('data');

        if(empty($data)) {
            return 'ERROR: Missing Parameters!';
        }
    
        $command = strtoupper($data);
    
        if(strpos('TERMINAL/INQUIRE', $command) !== false) {
            return 'RIT-V300'. "\n";
        }
    
        if(strpos('FILE/PROTECTION=OWNER:RWED ACCOUNTS.F', $command) !== false) {
            $_SESSION['ROOT_ACCOUNT'] = true;
            return "Root (5A8) \n";
        }
    
        if(strpos('HALT', $command) !== false) {
            $this->auth->logout();
            
            return 'SHUTTING DOWN...';
        }
    
        if(strpos('HALT RESTART', $command) !== false) {
            echo 'RESTARTING...';
            return file_get_contents(APP_STORAGE. 'text/boot.txt') . "\n";
        }
    
        if(strpos('HALT RESTART/MAINT', $command) !== false) {
            $_SESSION['MAINT_MODE'] = true;
            return file_get_contents(APP_STORAGE. 'text/maint.txt') . "\n";
        }
       
    }

    public function run($request, $response) { 

        $data = $request->getParam('data');

        if(empty($data)) {
            return 'ERROR: Missing Parameters!';
        }
    
        $command = strtoupper($data);
    
        if(!isset($_SESSION['ROOT_ACCOUNT'])) {
            return 'ERROR: Root Access Required!';
        }
        
        if(!isset($_SESSION['MAINT_MODE'])) {
            return 'ERROR: Maintenance Mode Required!';
        }
    
        if(strpos('LIST/ACCOUNTS.F', $command) !== false) {
            return listAccounts();
        }
    
        if(strpos('DEBUG/ACCOUNTS.F', $command) !== false) {
            $_SESSION['DEBUG_MODE'] = true;
            echo file_get_contents(APP_STORAGE. 'text/attempts.txt') . "\n";
            return dump($data);
        }
    }

}