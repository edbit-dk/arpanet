<?php

namespace App\Host\File;

use App\Host\File\FileModel as File;
use App\Host\Folder\FolderModel as Folder;
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

        // Loop through each top-level folder and format the structure
        foreach ($files as $file) {
            echo "[" . $file->file_name . "]\n";
        }
    }

    public static function open($file_name = '', $host_id = '')
    {
        $file = File::where('host_id', $host_id)
        ->where('file_name', $file_name)
        ->first();

        echo $file->content;
    }

    public static function files($host_id)
    {
        // Find the host
        $host = Host::find($host_id);

        if (!$host) {
            echo 'ERROR: Host not found';
            exit;
        }

        // Retrieve all top-level folders for the given host with subfolders and files
        $folders = Folder::where('host_id', $host_id)
                        ->whereNull('parent_id') // Only top-level folders
                        ->with(['subfolders.files', 'files'])
                        ->get();

        // Initialize an empty string to hold the structure
        $output = "Host ID: $host_id\n";

        // Loop through each top-level folder and format the structure
        foreach ($folders as $folder) {
            $output .= self::format($folder);
        }
    }

    // Helper function to recursively format folder structure with files
    private static function format($folder, $indent = 0)
    {
         // Create indentation based on folder level
         $indentation = str_repeat("  ", $indent);
         $output = "{$indentation}Folder: {$folder->folder_name}\n";
 
         // Add files in the folder
         foreach ($folder->files as $file) {
             $output .= "{$indentation}  - File: {$file->file_name} (Content: {$file->content})\n";
         }
 
         // Recursively add subfolders
         foreach ($folder->subfolders as $subfolder) {
             $output .= self::format($subfolder, $indent + 1);
         }
 
         return $output;
    }


}