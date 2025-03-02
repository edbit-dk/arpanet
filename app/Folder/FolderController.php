<?php

namespace App\Folder;

use App\AppController;

use App\Folder\FolderService as Folder;

use App\User\UserService as User;
use App\Host\HostService as Host;

class FolderController extends AppController
{
    public function pwd()
    {
        return Folder::pwd();
    }

    public function cd()
    {   
        if(!Folder::cd($this->request[0])) {
            echo 'UNKNOWN DIRECTORY.';
        }
    }
}