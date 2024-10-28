// Array to store command history
let stylesheets = 'public/css/';
let commandHistory = [];
let historyIndex = -1;
let currentDirectory = ''; // Variable to store the current directory
let isPasswordPrompt = false; // Flag to track if password prompt is active
let userPassword = ''; // Variable to store the password
let usernameForLogon = ''; // Variable to store the username for logon
let usernameForNewUser = ''; // Variable to store the username for new user
let isUsernamePrompt = false;
let currentCommand = '';

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

    if (response.startsWith("EXCACT")) {
        setTimeout(function() {
            setTimeout(function() {
                clearTerminal();
                sendCommand('welcome', '');
            }, 2500);

        }, 2000);
    }

    if (response.startsWith("Password")) {
        setTimeout(function() {
            setTimeout(function() {
                clearTerminal();
                sendCommand('welcome', '');
            }, 2500);

        }, 2000);
    }

    if (response.startsWith("Security")) {
            setTimeout(function() {

                loadText("Welcome to ARPANET!");

                setTimeout(function() {
                    clearTerminal();
                    sendCommand('welcome', '');
                }, 2500);

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
    loadText(input);
    commandHistory.push(input);
    historyIndex = commandHistory.length;

    // Clear the input field
    $('#command-input').val('');

    // Check if a username or password prompt is active
    if (isUsernamePrompt) {
        // Handle username input for newuser or login/logon
        if (input) {
            if (currentCommand === 'newuser') {
                usernameForNewUser = input;
                loadText("ENTER PASSWORD NOW:");
                isUsernamePrompt = false; // Username phase is over
                isPasswordPrompt = true; // Move to password phase
                $('#command-input').attr('type', 'password'); // Change input to password
            } else if (currentCommand === 'login' || currentCommand === 'logon') {
                usernameForLogon = input;
                loadText("ENTER PASSWORD:");
                isUsernamePrompt = false; // Username phase is over
                isPasswordPrompt = true; // Move to password phase
                $('#command-input').attr('type', 'password'); // Change input to password
            }
            return;
        } else {
            loadText("ERROR: WRONG USERNAME!");
            return;
        }
    }

    if (isPasswordPrompt) {
        // Handle password input
        handlePasswordPrompt();
        return;
    }

    const parts = input.split(' ');
    const command = parts[0];
    const args = parts.slice(1).join(' ');

    // Block newuser and login/logon commands if uplink is not set
    if (['newuser', 'logon', 'login'].includes(command) && !sessionStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required!");
        return; // Stop the process if uplink is not set
    }

    if (command === 'clear' || command === 'cls') {
        clearTerminal(); // Clear the terminal
    } else if (command === 'uplink') {
        sessionStorage.setItem('uplink', true);
        sendCommand(command, args); // Send the command to the server
    } else if (command === 'newuser') {
        if (!args) {
            loadText("ENTER USERNAME NOW:");
            isUsernamePrompt = true;
            currentCommand = 'newuser'; // Track the current command
            $('#command-input').attr('type', 'text'); // Ensure input is set to text for username
            return;
        }
        handleNewUser(args); // Handle newuser command with username provided
    } else if (command === 'logon' || command === 'login') {
        if (!args) {
            loadText("ENTER USERNAME:");
            isUsernamePrompt = true;
            currentCommand = command; // Track the current command as either 'logon' or 'login'
            $('#command-input').attr('type', 'text'); // Ensure input is set to text for username
            return;
        }
        handleLogon(args); // Handle logon command with username provided
    } else if (['logout', 'logoff', 'reboot', 'dc', 'restart', 'start', 'autoexec', 'exit'].includes(command)) {
        loadText("Please wait...");
        sendCommand(command, args); // Send the command to the server
        setTimeout(function() {
            clearTerminal();
            sendCommand('welcome', '');
        }, 2500);
    } else if (command === 'color') {
        setTheme(args); // Handle color setting
    } else {
        sendCommand(command, args); // Otherwise, send the command to the server
    }
}


// Function to handle the NEWUSER command
function handleNewUser(username) {
    if (!sessionStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required!");
        return;
    }

    if (!usernameForNewUser && !username) {
        loadText("ENTER USERNAME NOW:");
        isUsernamePrompt = true;
        $('#command-input').attr('type', 'text'); // Switch input to text for username
        return;
    }

    if (isPasswordPrompt) return; // Already prompting for password, do nothing
    isPasswordPrompt = true;
    usernameForNewUser = username;
    loadText("ENTER PASSWORD NOW:");
    $('#command-input').attr('type', 'password'); // Change input to password
}

// Function to handle the LOGON/LOGIN command
function handleLogon(username) {
    if (!sessionStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required!");
        return;
    }

    if (!usernameForLogon && !username) {
        loadText("ENTER USERNAME:");
        isUsernamePrompt = true;
        $('#command-input').attr('type', 'text'); // Switch input to text for username
        return;
    }

    if (isPasswordPrompt) return; // Already prompting for password, do nothing
    isPasswordPrompt = true;
    usernameForLogon = username;
    loadText("ENTER PASSWORD:");
    $('#command-input').attr('type', 'password'); // Change input to password
}

// Function to handle password prompt
function handlePasswordPrompt() {
    const password = $('#command-input').val().trim();
    userPassword = password;

    if (currentCommand === 'logon' || currentCommand === 'login') {
        sendCommand(currentCommand, usernameForLogon + ' ' + password);
        usernameForLogon = '';
    } else if (currentCommand === 'newuser') {
        sendCommand('newuser', usernameForNewUser + ' ' + password);
        usernameForNewUser = '';
    }

    isPasswordPrompt = false;
    $('#command-input').attr('type', 'text').val('');
}

// Function to handle password prompt response
function handlePasswordPromptResponse(response) {
    if (response.startsWith("ERROR") || response.startsWith("WARNING")) {
        loadText(response);
        isPasswordPrompt = false;
        $('#command-input').attr('type', 'text');
    } else if (response.startsWith("Password")) {
        loadText(response);
        setTimeout(function() {
            clearTerminal();
            sendCommand('welcome', '');
        }, 2500);
    } else {
        if (usernameForLogon) {
            sendCommand('logon', usernameForLogon + ' ' + userPassword);
        } else if (usernameForNewUser) {
            sendCommand('newuser', usernameForNewUser + ' ' + userPassword);
        }
    }
    $('#command-input').val('');
}

// Function to set text and background color
function setTheme(color) {
    $('#theme-color').attr('href', stylesheets + color + '-crt.css');
    localStorage.setItem('theme', color);
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

// Function to load text into terminal one letter at a time
function loadText(text) {
    let currentIndex = 0;
    const preContainer = $('<pre>').css({
        'white-space': 'pre-wrap',   // This preserves newlines and wraps long lines
        'word-wrap': 'break-word'    // Break words if they are too long for the line
    });

    $('#terminal').append(preContainer); // Append the container to the terminal

    function displayNextLetter() {
        if (currentIndex < text.length) {
            preContainer.append(text[currentIndex]);  // Append each letter
            currentIndex++;

            scrollToBottom();  // Ensure terminal auto-scrolls to the bottom
            setTimeout(displayNextLetter, 5);  // Delay between letters
        } else {
            $('#command-input').focus();  // Refocus on the input after all letters are displayed
        }
    }

    displayNextLetter();
}

// Function to simulate CRT effect
function simulateCRT(text, container) {
    const delay = 5;
    const inputField = $('#command-input').val('');

    let currentIndex = 0;
    let currentLine = $('<div>'); // Create a new lin

    // Add the line container to the DOM
    container.append(currentLine);

    function displayNextWord() {
        if (currentIndex < text.length) {
            let word = '';

            // Read the next word from the text
            while (currentIndex < text.length && text[currentIndex] !== ' ') {
                word += text[currentIndex];
                currentIndex++;
            }
            currentIndex++; // Skip the space

            // Create a span for the word and add it to the current line
            const wordElement = $('<span>').text(word + ' ');

            currentLine.append(wordElement);

            // Check if the word overflows the container
            if (container[0].scrollWidth > container[0].clientWidth) {
                // Remove the word from the current line
                wordElement.remove();

                // Move the word to a new line
                currentLine = $('<div>').append(wordElement);
                container.append(currentLine);
            }

            // Add delay for CRT effect
            setTimeout(displayNextWord, delay);
        } else {
            scrollToBottom();
        }
    }

    displayNextWord();
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
            clearTerminal();
            sendCommand('welcome', '');
        }, 10000);
    } else {

        setTimeout(function() {
            sendCommand('welcome', ''); // Send 'welcome' command if boot has been set
        }, 500);
    }
});
