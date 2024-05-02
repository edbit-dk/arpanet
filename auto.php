<?php

session_start(); // Start the session

// Define the home directory
define('HOME_DIRECTORY', __DIR__ . '/home/');

// Get the input from the POST request
$input = isset($_POST['input']) ? $_POST['input'] : '';

// Split the input by space and consider only the portion after the last space
$lastSpaceIndex = strrpos($input, ' ');
if ($lastSpaceIndex !== false) {
    $input = substr($input, $lastSpaceIndex + 1);
}

// Call a function to fetch autocomplete suggestions based on the input
$suggestions = getAutocompleteSuggestions($input);

// Return the suggestions as JSON
echo json_encode($suggestions);

// Function to fetch autocomplete suggestions
function getAutocompleteSuggestions($input) {
    // Get the current directory from session
    $currentDirectory = getCurrentDirectory();

    // Construct the full path to the current directory
    $fullPath = $currentDirectory;

    // Get list of files and directories
    $files = scandir($fullPath);

    // Filter out hidden files and directories
    $files = array_filter($files, function($file) {
        return $file[0] !== '.'; // Exclude hidden files (starting with dot)
    });

    // Filter the files based on the input
    $suggestions = array_filter($files, function($file) use ($input) {
        return strpos($file, $input) === 0; // Match files starting with the input
    });

    return array_values($suggestions); // Reset array keys
}

// Function to get the current working directory
function getCurrentDirectory() {
    // Get the current directory from session or set it to the user's home directory
    return $_SESSION['pwd'] ?? $_SESSION['username'];
}
