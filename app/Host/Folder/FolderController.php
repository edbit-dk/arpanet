<?php

namespace App\Host\Folder;

use Lib\Controller;
use Lib\Session;

use App\Host\File\FileService as File;
use App\Host\Folder\FolderService as Folder;

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