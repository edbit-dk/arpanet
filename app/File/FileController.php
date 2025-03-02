<?php

namespace App\File;

use App\AppController;

use App\File\FileService as File;
use App\Folder\FolderService as Folder;

use App\User\UserService as User;
use App\Host\HostService as Host;

class FileController extends AppController
{

    public function files()
    {
       $files = Host::data()->files()->where('folder_id', Folder::id())->get();

        // Loop through each top-level folder and format the structure
        foreach ($files as $file) {
            echo "$file->filename\n";
        }
    }

    public function ls()
    {
        if(!Folder::root()) {
            return $this->files();
        } else {
            return $this->dir();
        } 
    }

    public function dir()
    {
        $folders = Folder::list();

        // Loop through each top-level folder and format the structure
        foreach ($folders as $folder) {
            echo "$folder->foldername ";
        }
    }

    public function cat()
    {
        $file = Host::data()->file($$this->request[0], Folder::id());

        if($file) {
            echo $file->content;
        } else {
            echo 'UNKNOWN FILE';
        }
    }

    public function open()
    {
        return File::open($this->request[0], Host::data()->id);
    }

    public function echo()
    {
        $data = request()->get('data');

        $input = explode('>', $data);

        $file_content = str_replace("'", '', trim($input[0]));
        $file_name = trim($input[1]);

        File::create(
            User::data()->id, 
            Host::data()->id,
            0,
            $file_name,
            $file_content
        );
    }

    public function ftp()
    {
       echo ftp_transfer('test.txt', 'Hello World', 'put');
    }

}