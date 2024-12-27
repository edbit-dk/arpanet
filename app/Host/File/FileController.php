<?php

namespace App\Host\File;

use Lib\Controller;
use Lib\Session;

use App\Host\File\FileService as File;
use App\User\UserService as User;
use App\Host\HostService as Host;

class FileController extends Controller
{

    public function dir()
    {
        $files = Host::data()->files()->get();

        if($files->isEmpty()) {
            echo 'ERROR: Access Denied.';
            exit;
        }

        // Loop through each top-level folder and format the structure
        foreach ($files as $file) {
            echo "$file->id. [" . $file->file_name . "]\n";
        }
    }

    public function cat()
    {
        $data = parse_request('data');

        $file = Host::data()->file($data);
        
        echo $file->content;
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