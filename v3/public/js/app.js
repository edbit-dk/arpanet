// Array to store command history
let stylesheets = 'public/css/';
let commandHistory = [];
let historyIndex = -1;
let currentDirectory = ''; // Variable to store the current directory
let isPasswordPrompt = false; // Flag to track if password prompt is active
let userPassword = ''; // Variable to store the password
let usernameForLogon = ''; // Variable to store the username for logon
let usernameForNewUser = ''; // Variable to store the username for new user

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
        autocompleteCommand(); // Call autocomplete function on Tab key press
    }
});

// Function to send command to server
function sendCommand(command, data, queryString = '') {
    const query = window.location.search; // Get the current URL query string
    const route = command.split(" ")[0];
    $.ajax({
        type: 'GET',
        url: route.toLowerCase() + queryString,
        data: {
            data: data,
            query: query
        },
        success: function(response) {
            if (isPasswordPrompt) {
                handlePasswordPromptResponse(response); // Handle password prompt response
            } else {
                loadText(response); // Load response text into terminal
                handleRedirect(response); // Handle redirect if needed
            }
        }
    });
}

// Function to handle redirect
function handleRedirect(response) {

    if (response.startsWith("Contacting")) {
        setTimeout(function() {
            loadText("Accessing Mainframe...");

            setTimeout(function() {
                clearTerminal();
                sendCommand('welcome', '');
            }, 2000);

        }, 2000);
    }

    if (response.startsWith("Security")) {
            setTimeout(function() {

                if (!sessionStorage.getItem('uplink')) {
                    sessionStorage.setItem('uplink', 'true');
                }

                loadText("Connecting...");

                setTimeout(function() {
                    clearTerminal();
                    sendCommand('welcome', '');
                }, 2000);

            }, 2000);
    }
}

// Function to redirect to a specific query string
function redirectTo(url) {
    setTimeout(function() {
        window.location.href = url;
    }, 2500);
}

// Function to handle user input
function handleUserInput() {
    const input = $('#command-input').val().trim();

    if (input === '') return; // Ignore empty input

    // Append the command to the terminal and add it to history
    appendCommand(input);
    commandHistory.push(input);
    historyIndex = commandHistory.length;

    // Clear the input field
    $('#command-input').val('');

    const parts = input.split(' ');
    const command = parts[0];
    const args = parts.slice(1).join(' ');
    
    if (command === 'clear' || command === 'cls') {
        clearTerminal(); // Clear the terminal
    } else if (command === 'logon') {
        handleLogon(args); // Handle logon command
    } else if (['logout', 'logoff', 'reboot', 'dc', 'restart', 'start', 'autoexec '].includes(command)) {
        sendCommand(command, args); // Send the command to the server
        setTimeout(function() {
            location.reload();
        }, 1500);
    } else if (command === 'color') {
        setTheme(args); // Handle color setting
    } else {
        sendCommand(command, args); // Otherwise, send the command to the server
    }
}

// Function to set text and background color
function setTheme(color) {
    $('#theme-color').attr('href', stylesheets + color + '-crt.css');
    localStorage.setItem('theme', color);
}

// Function to handle creating a new user
function handleNewUser(username) {
    if (!username) {
        appendCommand("ERROR: NEW_USER [USERNAME]");
        return;
    }
    if (isPasswordPrompt) return;
    isPasswordPrompt = true;
    $('#command-input').attr('type', 'password');
    usernameForNewUser = username;
    appendCommand("ENTER PASSWORD NOW:");
}

// Function to handle the LOGON command
function handleLogon(username) {
    if (!sessionStorage.getItem('uplink')) {
        appendCommand("ERROR: Security Access Code Required!");
        return;
    }

    if (!username) {
        appendCommand("ERROR: Wrong Username.");
        isPasswordPrompt = false;
        $('#command-input').attr('type', 'text');
        return;
    }
    if (isPasswordPrompt) return;
    isPasswordPrompt = true;
    $('#command-input').attr('type', 'password');
    usernameForLogon = username;
    appendCommand("ENTER PASSWORD:");
}

// Function to handle password prompt
function handlePasswordPrompt() {
    const password = $('#command-input').val().trim();

    userPassword = password;

    if (usernameForLogon) {
        sendCommand('logon', usernameForLogon + ' ' + password);
        usernameForLogon = '';
    }

    isPasswordPrompt = true;
    $('#command-input').attr('type', 'text').val('');
}

// Function to handle password prompt response
function handlePasswordPromptResponse(response) {
    if (response.startsWith("ERROR") || response.startsWith("WARNING")) {
        appendCommand(response);
        isPasswordPrompt = false;
        $('#command-input').attr('type', 'text');
    } else if (response.startsWith("Password")) {
        appendCommand("\n");
        appendCommand(response);
        setTimeout(function() {
            location.reload();
        }, 2500);
    } else {
        if (usernameForLogon) {
            sendCommand('logon', usernameForLogon + ' ' + userPassword);
        }
    }
    $('#command-input').val('');
}

// Function to append command to terminal window
function appendCommand(command) {
    const commandElement = $('<div>').addClass('command-prompt').html(command);
    $('#terminal').append(commandElement);
    scrollToBottom(); 
}

// Function to clear terminal
function clearTerminal() {
    $('#terminal').empty();
}

// Function to load text into terminal one line at a time
function loadText(text) {
    const lines = text.split('\n');
    let lineIndex = 0;

    function displayNextLine() {
        if (lineIndex < lines.length) {
            const lineContainer = $('<div>');
            simulateCRT(lines[lineIndex], lineContainer);
            $('#terminal').append(lineContainer);
            scrollToBottom();
            lineIndex++;
            if (lineIndex < lines.length) {
                setTimeout(displayNextLine, 320);
            }
        }
    }

    displayNextLine();
}

// Function to simulate CRT effect
function simulateCRT(text, container) {
    const delay = 5;
    const inputField = $('#command-input').val('');

    let currentIndex = 0;

    function displayNextChar() {
        if (currentIndex < text.length) {
            let char = text[currentIndex];
            if (char === ' ') char = '\u00A0';
            container.append($('<span>').html(char));
            currentIndex++;
            setTimeout(displayNextChar, delay);
        } else {
            scrollToBottom();
        }
    }

    displayNextChar();
}

// Function to scroll the terminal window to the bottom
function scrollToBottom() {
    const terminal = document.getElementById('terminal-wrapper');
    terminal.scrollTop = terminal.scrollHeight;
    $('#terminal-wrapper').scrollTop($('#terminal-wrapper')[0].scrollHeight);
}

// Function to load the saved theme from localStorage
function loadSavedTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme);
    }
}

// Function to autocomplete command
function autocompleteCommand() {
    const inputElement = $('#command-input');
    let input = inputElement.val().trim();
    if (input === '') return;

    $.ajax({
        type: 'GET',
        url: 'auto.php',
        data: { input: input },
        success: function(response) {
            const suggestions = JSON.parse(response);
            if (suggestions.length > 0) {
                const suggestion = suggestions[0];
                const lastSpaceIndex = input.lastIndexOf(' ');
                const prefix = input.substring(0, lastSpaceIndex + 1);
                const suffix = input.substring(lastSpaceIndex + 1);
                inputElement.val(prefix + suggestion);
                inputElement[0].setSelectionRange(prefix.length + suggestion.length, prefix.length + suggestion.length);
            }
        }
    });
}

// Event listener for when the DOM content is loaded
$(document).ready(function() {
    // Load the saved theme when the document is ready
    loadSavedTheme();

    // Check if 'boot' command has been sent during the current session
    if (!sessionStorage.getItem('boot')) {

        setTimeout(function() {
            sendCommand('boot', ''); // Send 'boot' command
        }, 500);
        
        setTimeout(function() {
            sessionStorage.setItem('boot', true); // Set 'boot' flag in sessionStorage
            location.reload(); // Reload the page after setting the boot flag
        }, 10000);
    } else {

        setTimeout(function() {
            sendCommand('welcome', ''); // Send 'welcome' command if boot has been set
        }, 500);
    }
});
