<?php

namespace App\Folder;

use Lib\Controller;
use Lib\Session;

use App\File\FileService as File;
use App\Folder\FolderService as Folder;

use App\User\UserService as User;
use App\Host\HostService as Host;

class FolderController extends Controller
{
    public function pwd()
    {
        return Folder::pwd();
    }

    public function cd()
    {
        $data = parse_request('data');
        
        if(!Folder::cd($data[0])) {
            echo 'ERROR: Unknown Directory.';
        }
    }
}