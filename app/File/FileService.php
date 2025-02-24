<?php

namespace App\File;

use App\File\FileModel as File;
use App\Folder\FolderModel as Folder;
use App\User\UserModel as User;
use App\Host\HostModel as Host;

class FileService
{
    public static function create($user_id, $host_id, $folder_id, $file_name, $content): FileModel  
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
    

    public static function list($host_id, $user_id = '')
    {
        $files = File::where('host_id', $host_id)
        ->orWhere('user_id', $user_id)
        ->get();

        if($files->isEmpty()) {
            echo 'ERROR: Access Denied.';
            exit;
        }

        // Loop through each top-level folder and format the structure
        foreach ($files as $file) {
            echo "$file->id. [" . $file->file_name . "]\n";
        }
    }

    public static function open($file_name = '', $host_id = '')
    {

        $file = File::where('file_name', $file_name)
        ->orWhere('host_id', $host_id)
        ->orWhere('id', $file_name)
        ->first();

        if(empty($file->content)) {
            echo 'ERROR: File not Found.';
        } else {
            echo $file->content;
        }
    }

}