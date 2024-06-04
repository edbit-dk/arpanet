<?php

// Function to get the current working directory relative to HOME_DIRECTORY
function getCurrentDirectory($showFullPath = false) {
    global $server_id;

    $currentDirectory = $_SESSION['pwd'] ?? HOME_DIRECTORY . DIRECTORY_SEPARATOR . $server_id;
    if ($showFullPath) {
        return str_replace(realpath(HOME_DIRECTORY), '', $currentDirectory);
    } else {
        return $currentDirectory;
    }
}

// Function to handle the 'echo' command to write content to a file
function echoToFile($data) {
    // Separate the content and filename
    if(!strpos($data, '>')) {
        return $data;
    }

    $parts = explode('>', $data, 2);
    $content = trim($parts[0]);
    $filename = trim($parts[1]);

    // Check if the filename is empty
    if (empty($filename)) {
        return "ERROR: FILENAME MISSING";
    }

    // Construct the full path
    $path = getCurrentDirectory() . DIRECTORY_SEPARATOR . $filename;

    // Check if the file exists, if not, create it
    if (!file_exists($path)) {
        if (touch($path)) {
            $message = "$filename\n";
        } else {
            return "ERROR: ACTION FAILED";
        }
    } else {
        $message = "ERROR: {$filename} EXISTS";
    }

    // Write content to the file
    if (file_put_contents($path, $content) !== false) {
        return $message . "SAVING: $filename";
    } else {
        return "ERROR: ACTION FAILED";
    }
}

// Function to create a new file
function createFile($data) {
    $filename = strtok($data, ' ');
    $path = getFullDirectory();
    if (strpos($path . $filename, getFullDirectory()) !== 0) {
        return "ERROR: ACCESS DENIED";
    }
    if (file_exists($path . $filename)) {
        return "ERROR: {$filename} EXISTS";
    } elseif (touch($path . $filename)) {
        return "SAVED: $filename";
    } else {
        return "ERROR: ACTION FAILED";
    }
}

// Function to create a new folder
function createFolder($data) {
    $foldername = $data;
    $currentUserDirectory = getCurrentDirectory();
    $newFolder = $currentUserDirectory . DIRECTORY_SEPARATOR . $foldername;
    if (strpos($newFolder, $currentUserDirectory) !== 0) {
        return "ERROR: ACCESS DENIED";
    }
    if (file_exists($newFolder)) {
        return "{$newFolder} EXISTS";
    } elseif (mkdir($newFolder)) {
        return "SAVED: $newFolder";
    } else {
        return "ERROR: FAILED";
    }
}

// Function to write content to a file
function writeFile($data) {
    list($filename, $content) = explode('>', $data, 2);
    $file = getCurrentDirectory() . DIRECTORY_SEPARATOR . $filename;
    if (!file_exists($file)) {
        return "ERROR: {$filename} MISSING";
    }
    if (file_put_contents($file, $content) !== false) {
        return "SAVING: $filename";
    } else {
        return "ERROR: ACTION FAILED";
    }
}

// Function to change the current working directory
function changeDirectory($data) {
    global $server, $server_id;
    $homeDirectory = getCurrentDirectory();

    if (!isset($_SESSION['pwd'])) {
        $_SESSION['pwd'] = $homeDirectory;
    }

    if ($data === '..') {
        // Handle navigating up one level
        $parentDirectory = realpath(dirname($_SESSION['pwd']));
        if (strpos($parentDirectory, realpath($homeDirectory)) === 0) {
            $_SESSION['pwd'] = $parentDirectory;
            return basename(getCurrentDirectory());
        } else {
            return "ERROR: ACCESS DENIED";
        }
    }

    $currentUserDirectory = $_SESSION['pwd'];
    $requestedDirectory = realpath($currentUserDirectory . DIRECTORY_SEPARATOR . $data);

    // Ensure the requested directory is within the user's home directory
    if (strpos($requestedDirectory, realpath($homeDirectory)) !== 0) {
        return "ERROR: ACCESS DENIED";
    }

    if (empty($data)) {
        $_SESSION['pwd'] = $homeDirectory;
        return basename(getCurrentDirectory());
    } elseif (is_dir($requestedDirectory)) {
        $_SESSION['pwd'] = $requestedDirectory;
        return basename(getCurrentDirectory());
    } else {
        return "ERROR: {$requestedDirectory} MISSING";
    }
}


// Function to move a file or folder
function moveFileOrFolder($data) {
    global $server_id;

    // Get the current user's directory from session
    $currentUserDirectory = getCurrentDirectory();

    // Split the data into source and destination
    list($source, $destination) = explode(' ', $data, 2);

    // Construct the full paths for source and destination
    $sourceFullPath = $currentUserDirectory . DIRECTORY_SEPARATOR . $source;
    $destinationFullPath = $currentUserDirectory . DIRECTORY_SEPARATOR . $destination;

    // Check if both source and destination are within the user's directory
    if (strpos($sourceFullPath, $currentUserDirectory) !== 0 || strpos($destinationFullPath, $currentUserDirectory) !== 0) {
        return "ERROR: ACCESS DENIED";
    }

    // Check if the source file or folder exists
    if (!file_exists($sourceFullPath)) {
        return "ERROR: {$sourceFullPath} MISSING";
    }

    // Check if the destination directory exists
    if (!is_dir(dirname($destinationFullPath))) {
        return "ERROR: {$destinationFullPath} MISSING";
    }

    // Attempt to move the file or folder
    if (rename($sourceFullPath, $destinationFullPath)) {
        return "ERROR: FOLDER OR FILE MISSING";
    } else {
        return "ERROR: ACTION FAILED";
    }
}

// Function to read the content of a file
function readFileContent($data) {

    
    // Get the current user's directory from session
    $currentUserDirectory = getCurrentDirectory();

    $filename = $data;
    $fullPath = $currentUserDirectory . DIRECTORY_SEPARATOR . $filename;

    if (!empty($filename) && file_exists($fullPath)) {
        return file_get_contents($fullPath);
    } else {
        return "ERROR: ACCESS DENIED";
    }
}

// Function to delete a file or folder
function deleteFileOrFolder($data) {
    $path = realpath(getCurrentDirectory() . DIRECTORY_SEPARATOR . $data);

    // Prevent deletion of the main pwd directory
    if ($path === realpath(HOME_DIRECTORY . $_SESSION['username'])) {
        return "ERROR: ACCESS DENIED";
    }

    if (!file_exists($path)) {
        return "ERROR: ACCESS DENIED";
    }

    if (is_file($path)) {
        if (unlink($path)) {
            return "DELETED";
        } else {
            return "ERROR: ACTION FAILED";
        }
    } elseif (is_dir($path)) {
        if (rrmdir($path)) {
            return "DELETED";
        } else {
            return "ERROR: ACTION FAILED";
        }
    }
    return "ERROR: ACCESS DENIED";
}

// Recursive function to delete a folder and its contents
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $object)) {
                    rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                } else {
                    unlink($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        }
        reset($objects);
        return rmdir($dir);
    }
    return false;
}

// Function to get the full directory path
function getFullDirectory() {
    return HOME_DIRECTORY . DIRECTORY_SEPARATOR . getCurrentDirectory() . DIRECTORY_SEPARATOR;
}

// Function to list files in the specified directory or current directory if not provided
function listFiles() {
    $directory = $_SESSION['pwd'] ?? HOME_DIRECTORY;
    $files = scandir($directory);
    $files = array_filter($files, function($file) {
        return !in_array($file, ['.', '..']); // Exclude "." and ".." from the result
    });
    
    $formattedFiles = array_map(function($file) {
        $filename = pathinfo($file, PATHINFO_FILENAME);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        if (!empty($extension)) {
            return "[" . $filename . "." . $extension . "]";
        } else {
            return "[" . $filename . "]";
        }
    }, $files);
    
    return implode("\n", $formattedFiles);
}



