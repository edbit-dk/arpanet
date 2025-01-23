<?php

namespace App\Host\File;

use Lib\Controller;
use Lib\Session;

use App\Host\File\FileService as File;
use App\Host\Folder\FolderService as Folder;

use App\User\UserService as User;
use App\Host\HostService as Host;

class FileController extends Controller
{

    public function files()
    {
       $files = Host::data()->files()->where('folder_id', Folder::id())->get();

        // Loop through each top-level folder and format the structure
        foreach ($files as $file) {
            echo "$file->file_name\n";
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
            echo "[$folder->folder_name]\n";
        }
    }

    public function cat()
    {
        $data = parse_request('data');

        $file = Host::data()->file($data, Folder::id());

        if($file) {
            echo $file->content;
        } else {
            echo 'ERROR: Unknown File.';
        }
    }

    public function open()
    {
        $data = parse_request('data');

        return File::open($data[0], Host::data()->id);
    }

    public function echo()
    {
        $data = request()->get('data');

        $input = explode('>', $data);

        $file_content = str_replace("'", '', trim($input[0]));
        $file_name = trim($input[1]);

        $file = File::create(
            User::data()->id, 
            Host::data()->id,
            0,
            $file_name,
            $file_content
        );

        var_dump($file );
    }

    public function ftp()
    {
        
    }

}