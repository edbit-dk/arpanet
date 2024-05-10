<?php

// Function to get the current working directory relative to HOME_DIRECTORY
function getCurrentDirectory($showFullPath = false) {
    $currentDirectory = $_SESSION['pwd'] ?? HOME_DIRECTORY;
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
        return "Filename not provided.";
    }

    // Construct the full path
    $path = getCurrentDirectory() . DIRECTORY_SEPARATOR . $filename;

    // Check if the file exists, if not, create it
    if (!file_exists($path)) {
        if (touch($path)) {
            $message = "File created successfully: $filename\n";
        } else {
            return "Failed to create file.";
        }
    } else {
        $message = "File exists. ";
    }

    // Write content to the file
    if (file_put_contents($path, $content) !== false) {
        return $message . "Content written to file successfully: $filename";
    } else {
        return "Failed to write content to file.";
    }
}

// Function to create a new file
function createFile($data) {
    $filename = strtok($data, ' ');
    $path = getFullDirectory();
    if (strpos($path . $filename, getFullDirectory()) !== 0) {
        return "Access denied.";
    }
    if (file_exists($path . $filename)) {
        return "File already exists.";
    } elseif (touch($path . $filename)) {
        return "File created successfully: $filename";
    } else {
        return "Failed to create file.";
    }
}

// Function to create a new folder
function createFolder($data) {
    $foldername = $data;
    $currentUserDirectory = $_SESSION['pwd'] ?? HOME_DIRECTORY;
    $newFolder = $currentUserDirectory . DIRECTORY_SEPARATOR . $foldername;
    if (strpos($newFolder, $currentUserDirectory) !== 0) {
        return "Access denied.";
    }
    if (file_exists($newFolder)) {
        return "Folder already exists.";
    } elseif (mkdir($newFolder)) {
        return "Folder created successfully: $foldername";
    } else {
        return "Failed to create folder.";
    }
}

// Function to write content to a file
function writeFile($data) {
    list($filename, $content) = explode('>', $data, 2);
    $file = getCurrentDirectory() . DIRECTORY_SEPARATOR . $filename;
    if (!file_exists($file)) {
        return "File does not exist.";
    }
    if (file_put_contents($file, $content) !== false) {
        return "Content written to file successfully: $filename";
    } else {
        return "Failed to write content to file.";
    }
}

// Function to change the current working directory
function changeDirectory($data) {
    if ($data === '..') {
        // Handle navigating up one level
        $parentDirectory = realpath(dirname($_SESSION['pwd'] ?? HOME_DIRECTORY));
        if ($parentDirectory !== HOME_DIRECTORY . $_SESSION['username']) {
            $_SESSION['pwd'] = $parentDirectory;
            return basename(getCurrentDirectory());
        } else {
            return "Already at the top level directory.";
        }
    }
    $currentUserDirectory = $_SESSION['pwd'] ?? HOME_DIRECTORY;
    $requestedDirectory = realpath($currentUserDirectory . DIRECTORY_SEPARATOR . $data);
    if (strpos($requestedDirectory, realpath($currentUserDirectory)) !== 0) {
        return "Access denied or folder not found.";
    }
    if (empty($data)) {
        $_SESSION['pwd'] = HOME_DIRECTORY . $_SESSION['username'];
        return basename(getCurrentDirectory());
    } elseif (is_dir($requestedDirectory)) {
        $_SESSION['pwd'] = $requestedDirectory;
        return basename(getCurrentDirectory());
    } else {
        return "Directory does not exist.";
    }
}

// Function to move a file or folder
function moveFileOrFolder($data) {
    // Get the current user's directory from session
    $currentUserDirectory = $_SESSION['pwd'] ?? HOME_DIRECTORY . $_SESSION['username'];

    // Split the data into source and destination
    list($source, $destination) = explode(' ', $data, 2);

    // Construct the full paths for source and destination
    $sourceFullPath = $currentUserDirectory . DIRECTORY_SEPARATOR . $source;
    $destinationFullPath = $currentUserDirectory . DIRECTORY_SEPARATOR . $destination;

    // Check if both source and destination are within the user's directory
    if (strpos($sourceFullPath, $currentUserDirectory) !== 0 || strpos($destinationFullPath, $currentUserDirectory) !== 0) {
        return "Access denied.";
    }

    // Check if the source file or folder exists
    if (!file_exists($sourceFullPath)) {
        return "Source file or folder does not exist.";
    }

    // Check if the destination directory exists
    if (!is_dir(dirname($destinationFullPath))) {
        return "Destination directory does not exist.";
    }

    // Attempt to move the file or folder
    if (rename($sourceFullPath, $destinationFullPath)) {
        return "File or folder moved successfully.";
    } else {
        return "Failed to move file or folder.";
    }
}

// Function to read the content of a file
function readFileContent($data) {
    $currentDirectory = getFullDirectory();
    $filename = $data;
    $fullPath = realpath($currentDirectory . DIRECTORY_SEPARATOR . $filename);
    if (!empty($filename) && file_exists($fullPath)) {
        return file_get_contents($fullPath);
    } else {
        return "File not found or access denied.";
    }
}

// Function to delete a file or folder
function deleteFileOrFolder($data) {
    $currentUserDirectory = $_SESSION['pwd'] ?? HOME_DIRECTORY;
    $path = realpath($currentUserDirectory . DIRECTORY_SEPARATOR . $data);

    // Prevent deletion of the main pwd directory
    if ($path === realpath(HOME_DIRECTORY . $_SESSION['username'])) {
        return "Cannot delete main directory.";
    }

    if (strpos($path, $currentUserDirectory) !== 0 || !file_exists($path)) {
        return "Access denied or file/folder not found.";
    }

    if (is_file($path)) {
        if (unlink($path)) {
            return "File deleted successfully.";
        } else {
            return "Failed to delete file.";
        }
    } elseif (is_dir($path)) {
        if (rrmdir($path)) {
            return "Folder deleted successfully.";
        } else {
            return "Failed to delete folder.";
        }
    }
    return "Path does not exist or is not a file/folder.";
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
    return implode(" ", $files);
}

