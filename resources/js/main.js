// Array to store command history
let path_public = 'public/';
let stylesheets = path_public + 'css/';
let commandHistory = [];
let historyIndex = -1;
let currentDirectory = ''; // Variable to store the current directory
let isPasswordPrompt = false; // Flag to track if password prompt is active
let userPassword = ''; // Variable to store the password
let usernameForLogon = ''; // Variable to store the username for logon
let usernameForNewUser = ''; // Variable to store the username for new user
let isUsernamePrompt = false;
let currentCommand = '';
let commands = [];
let cmd = '';

// Event listener for when the DOM content is loaded
$(document).ready(function() {
    // Load the saved theme when the document is ready
    loadSavedTheme();

    // Load the saved term when the document is ready
    loadSavedTermMode();

    //Check commands available
    listCommands();

    // Check if 'boot' command has been sent during the current session
    if (!sessionStorage.getItem('boot')) {

        setTimeout(function() {
            sendCommand('boot', ''); // Send 'boot' command
        }, 500);
        
        setTimeout(function() {
            sessionStorage.setItem('boot', true); // Set 'boot' flag in sessionStorage
            clearTerminal();
            sendCommand('welcome', '');
        }, 10000);
    } else {

        setTimeout(function() {
            sendCommand('welcome', ''); // Send 'welcome' command if boot has been set
        }, 500);
    }
});

// Event listener for handling keydown events
$('#command-input').keydown(function(e) {
    if (e.key === 'Enter') {
        if (isPasswordPrompt) {
            handlePasswordPrompt(); // Handle password prompt on Enter key press
        } else {
            handleUserInput(); // Handle user input on Enter key press
        }
    } else if (e.key === 'ArrowUp') {
        // Navigate command history on ArrowUp key press
        if (historyIndex > 0) {
            historyIndex--;
            $('#command-input').val(commandHistory[historyIndex]);
        }
    } else if (e.key === 'ArrowDown') {
        // Navigate command history on ArrowDown key press
        if (historyIndex < commandHistory.length - 1) {
            historyIndex++;
            $('#command-input').val(commandHistory[historyIndex]);
        } else {
            // Clear input when reaching the end of history
            historyIndex = commandHistory.length;
            $('#command-input').val('');
        }
    } else if (e.key === 'Tab') {
        e.preventDefault(); // Prevent default tab behavior
        autocomplete(); // Call autocomplete function on Tab key press
    }
});

// Fetch commands from the server based on the user's status
function listCommands() {
    fetch('help?data=auto')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                commands = data.filter(item => typeof item === 'string'); // Keep only strings
            } else {
                console.error('Invalid commands data:', data);
            }
        })
        .catch(error => console.error('Error fetching commands:', error));
}


function autocomplete() {
    const inputField = $('#command-input');
    const currentText = inputField.val().trim();

    // Find commands that match the current input
    const matches = commands.filter(cmd => typeof cmd === 'string' && cmd.startsWith(currentText));


    if (matches.length === 1) {
        // If only one match, autocomplete the input
        inputField.val(matches[0]);
    } else if (matches.length > 1) {
        // If multiple matches, find the common prefix
        const commonPrefix = findCommonPrefix(matches);
        if (commonPrefix.length > currentText.length) {
            // Autocomplete the input to the common prefix
            inputField.val(commonPrefix);
        } else {
            // Show all matches in the terminal as suggestions
            loadText(`${matches.join(' ')}`);
        }
    } else {
        // No matches
        loadText('');
    }
}

// Utility function to find the common prefix of an array of strings
function findCommonPrefix(strings) {
    if (!strings.length) return '';
    let prefix = strings[0];
    for (let i = 1; i < strings.length; i++) {
        while (!strings[i].startsWith(prefix)) {
            prefix = prefix.slice(0, -1);
            if (!prefix) break;
        }
    }
    return prefix;
}
