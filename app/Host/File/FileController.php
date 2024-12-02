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
        File::list(Host::data()->id, User::auth());
    }

    public function open()
    {
        $data = explode(' ', request()->get('data'));

        File::open($data[0], Host::data()->id);
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

    public function mail()
    {
        
    }

}