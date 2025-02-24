<?php

namespace App\Folder;

use App\File\FileModel as File;
use App\Folder\FolderModel as Folder;

use App\User\UserModel as User;
use App\User\UserService as Auth;

use App\Host\HostService as Server;
use App\Host\HostModel as Host;

use Lib\Session;

class FolderService
{
    private static $folder = 'folder_id';
    private static $pwd = 'user_pwd';
    private static $root_dir = '/';

    public static function pwd()
    {
        if(!Session::has(self::$pwd)) {
            Session::set(self::$pwd, self::$root_dir);
        }

        return Session::get(self::$pwd);
    }

    public static function reset()
    {
        if(Session::has(self::$pwd)) {
            Session::remove(self::$pwd);
        }
    }

    public static function root()
    {
        if(self::pwd() == self::$root_dir){
            return true;
        }

        return false;
            
    }

    public static function id()
    {
        if(Session::has(self::$folder)) {
            return Session::get(self::$folder);
        }
        return false;
    }

    public static function cd($dir = '')
    {
        if(str_contains($dir, '../')) {
            $dir = str_replace('../', '', $dir);
            return Session::set(self::$pwd, self::$root_dir . $dir);
        }

        if($dir == '..' || empty($dir)) {
            return Session::set(self::$pwd, self::$root_dir);
        }

        if($folder = Folder::where('foldername', $dir)->first()) {
            Session::set(self::$folder, $folder->id);
            return Session::set(self::$pwd, self::$root_dir . $dir);
        }

        return false;
    }

    public static function create($user_id, $host_id, $folder_id, $file_name, $content)  
    {
        // Fetch the current authenticated user
        $user = User::find($user_id);
        $host = Host::find($host_id);
        $folder = Folder::find($folder_id);

        if (!$folder || !$user || !$host) {
            echo 'ERROR: Invalid folder, user, or host';
            exit;
        }

        // Check if the user is authorized to create or modify files in this folder
        if ($folder->isOwnedBy($user)) {
            // Check if the file already exists
            $existingFile = File::where('file_name', $file_name)
                                ->where('folder_id', $folder_id)
                                ->first();

            if ($existingFile) {
                echo 'ERROR: File already exists';
                exit;
            }

            // Create the new file
            $file = new File();
            $file->file_name = $file_name;
            $file->content = $content;
            $file->folder_id = $folder->id;
            $file->user_id = $user->id;
            $file->host_id = $host->id;
            $file->save();

            echo 'SUCCESS: File created successfully';
            return $file;
        } else {
            echo 'ERROR: Unauthorized';
            exit;
        }
    }
    

    public static function list()
    {
        $host_id = Server::id();
        $user_id = Auth::id();

        return Folder::get();
        
    }
}