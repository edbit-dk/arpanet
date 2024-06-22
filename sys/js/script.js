// Array to store command history
let stylesheets = 'sys/css/';
let commandHistory = [];
let historyIndex = -1;
let currentDirectory = ''; // Variable to store the current directory
let isPasswordPrompt = false; // Flag to track if password prompt is active
let userPassword = ''; // Variable to store the password
let usernameForLogon = ''; // Variable to store the username for logon
let usernameForNewUser = ''; // Variable to store the username for new user

// Event listener for handling keydown events
document.getElementById('command-input').addEventListener('keydown', function(e) {
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
            document.getElementById('command-input').value = commandHistory[historyIndex];
        }
    } else if (e.key === 'ArrowDown') {
        // Navigate command history on ArrowDown key press
        if (historyIndex < commandHistory.length - 1) {
            historyIndex++;
            document.getElementById('command-input').value = commandHistory[historyIndex];
        } else {
            // Clear input when reaching the end of history
            historyIndex = commandHistory.length;
            document.getElementById('command-input').value = '';
        }
    } else if (e.key === 'Tab') {
        e.preventDefault(); // Prevent default tab behavior
        autocompleteCommand(); // Call autocomplete function on Tab key press
    }
});

// Function to send command to server
function sendCommand(command, data, queryString = '') {
    const query = window.location.search; // Get the current URL query string
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'server.php' + queryString, true); // Include the query string in the URL
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = xhr.responseText;
            if (isPasswordPrompt) {
                handlePasswordPromptResponse(response); // Handle password prompt response
            } else {
                loadText(response); // Load response text into terminal
                handleRedirect(response); // Handle redirect if needed
            }
        }
    };
    xhr.send('command=' + encodeURIComponent(command) + '&data=' + encodeURIComponent(data) + '&query=' + encodeURIComponent(query));
}

// Function to handle redirect
function handleRedirect(response) {
    if (response.startsWith("Contacting")) {
        const regex = /Server:\s*(\S+)/; // Regular expression to match "Server: " followed by any sequence of non-whitespace characters
        const match = response.match(regex); // Match the regular expression in the response
        if (match) {
            const server_id = match[1]; // Extract the first capture group (the value after "Server:")
            setTimeout(function() {
                loadText('Accessing Mainframe...');
            }, 1500);

            setTimeout(function() {
                redirectTo('?server=' + server_id); // Redirect to a specific query string using the server number
            }, 1500);

            
        }
    }

    if (response.startsWith("ACCESS")) {
        const regex = /CODE:\s*(\S+)/;// Regular expression to match "Server: " followed by any sequence of non-whitespace characters
        const match = response.match(regex); // Match the regular expression in the response
        if (match) {
            const access_code = match[1]; // Extract the first capture group (the value after "Server:")
            
            if (!sessionStorage.getItem('code')) {
                sessionStorage.setItem('code', 'true'); // Set flag in sessionStorage
            }
            
            setTimeout(function() {
                appendCommand("\n");
                loadText("Security Access Code Sequence Accepted.\nWelcome to PoseidoNet!");

                setTimeout(function() {
                    redirectTo('?server=0&code=' + access_code); // Redirect to a specific query string using the server number
                }, 1500);

            }, 1500);
           
        }
    }
}


// Function to redirect to a specific query string
function redirectTo(url) {
    // Replace 'your_redirect_url?specific_query_string' with the URL you want to redirect to along with the specific query string you want to include
    setTimeout(function() {
        window.location.href = url;
    }, 2500); // Delay of 1000 milliseconds (1 second) before reloading
}

// Function to handle user input
function handleUserInput() {
    const input = document.getElementById('command-input').value.trim();

    if (input === '') return; // Ignore empty input

    // Append the command to the terminal and add it to history
    appendCommand(input);
    commandHistory.push(input);
    historyIndex = commandHistory.length;

    // Clear the input field
    document.getElementById('command-input').value = '';

    // Check if the command is 'clear' or 'cd'
    const parts = input.split(' ');
    const command = parts[0];
    const args = parts.slice(1).join(' ');
    if (command === 'clear' || command === 'cls') {
        clearTerminal(); // Clear the terminal
    } else if (command === 'logon') {
        handleLogon(args); // Handle logon command
    } else if (command === 'logout' || command === 'logoff' || command === 'reboot' || command === 'dc' || command === 'restart' || command === 'start' || command === 'autoexec ') {
        sendCommand(command, args); // Otherwise, send the command to the server
        setTimeout(function() {
            location.reload();
        }, 1500); // Delay of 100 milliseconds
        return; // Exit function after reload is scheduled
    } else if (command === 'register') {
        handleNewUser(args); // Handle new user creation
    } else if (command === 'color') {
        setTheme(args); // Handle color setting
    } else {
        sendCommand(command, args); // Otherwise, send the command to the server
    }
}

 // Function to set text and background color
 function setTheme(color) {
    const stylesheetLink = document.getElementById('theme-color');
    stylesheetLink.href = stylesheets + color + '-crt.css';
    localStorage.setItem('theme', color); // Save the theme to localStorage
    // appendCommand(`Changed theme to ${color}`);
}

// Function to handle creating a new user
function handleNewUser(username) {
    if (!username) {
        appendCommand("ERROR: NEW_USER [USERNAME]");
        return;
    }
    if (isPasswordPrompt) return; // Prevent re-triggering the password prompt
    isPasswordPrompt = true;
    document.getElementById('command-input').type = 'password'; // Change input type to password
    usernameForNewUser = username; // Store the username for new user
    appendCommand("ENTER PASSWORD NOW:"); // Prompt for password
}

// Function to handle the LOGON command
function handleLogon(username) {
    if (!sessionStorage.getItem('code')) {
        appendCommand("ERROR: Security Access Code Required!");
        return;
    }

    if (!username) {
        appendCommand("ERROR: Wrong Username.");
        isPasswordPrompt = false;
        document.getElementById('command-input').type = 'text'; // Change input type to text
        return;
    }
    if (isPasswordPrompt) return; // Prevent re-triggering the password prompt
    isPasswordPrompt = true;
    document.getElementById('command-input').type = 'password'; // Change input type to password
    usernameForLogon = username; // Store the username for logon
    appendCommand("ENTER PASSWORD:"); // Prompt for password
}

// Function to handle password prompt
function handlePasswordPrompt() {
    const password = document.getElementById('command-input').value.trim();

    userPassword = password; // Store the password

    if (usernameForNewUser) {
        sendCommand('register', usernameForNewUser + ' ' + password);
        usernameForNewUser = ''; // Reset the username for new user
    } else if (usernameForLogon) {
        sendCommand('logon', usernameForLogon + ' ' + password);
        usernameForLogon = ''; // Reset the username for logon
    }

    // Reset input field and disable password prompt after sending the password
    isPasswordPrompt = true;
    document.getElementById('command-input').type = 'text'; // Change input type back to text
    document.getElementById('command-input').value = '';
}



function handlePasswordPromptResponse(response) {
    if (response.startsWith("ERROR") || response.startsWith("WARNING")) {
        appendCommand(response); // Display response in terminal
        isPasswordPrompt = false; // Disable password prompt
        document.getElementById('command-input').type = 'text'; // Change input type to text
    } else if (response.startsWith("Password")) {
        appendCommand("\n");
        appendCommand(response); // Display "LOGGING IN..." message
        setTimeout(function() {
            location.reload();
        }, 2500); // Delay of 1000 milliseconds (1 second) before reloading
    } else {
        // Only resend the command if it's not "LOGGING IN..." or an error
        if (usernameForNewUser) {
            sendCommand('register', usernameForNewUser + ' ' + userPassword);
        } else if (usernameForLogon) {
            sendCommand('logon', usernameForLogon + ' ' + userPassword);
        }
    }

    // Reset input field
    document.getElementById('command-input').value = '';
}

// Function to append command to terminal window
function appendCommand(command) {
    const terminal = document.getElementById('terminal');
    const commandElement = document.createElement('div');
    commandElement.classList.add('command-prompt'); // Add command prompt class
    commandElement.innerHTML = command;
    terminal.appendChild(commandElement);
    scrollToBottom(); // Ensure scrolling after appending
}

// Function to clear terminal
function clearTerminal() {
    const terminal = document.getElementById('terminal');
    terminal.innerHTML = ''; // Clear the content of the terminal
}

// Function to load text into terminal one line at a time
function loadText(text) {
    const textContainer = document.getElementById('terminal');
    const lines = text.split('\n');
    let lineIndex = 0;

    function displayNextLine() {
        if (lineIndex < lines.length) {
            const lineContainer = document.createElement('div');
            simulateCRT(lines[lineIndex], lineContainer); // Apply CRT effect to the line
            textContainer.appendChild(lineContainer);
            scrollToBottom(); // Scroll to the bottom after loading each line
            lineIndex++;
            if (lineIndex < lines.length) {
                setTimeout(displayNextLine, 320); // Adjust delay as needed
            }
        }
    }

    displayNextLine(); // Start displaying lines
}

// Function to simulate CRT effect
function simulateCRT(text, container) {
    const delay = 2; // Delay between each character in milliseconds
    const distortionChance = 0.5; // Chance of random distortion per character
    const inputField = document.getElementById('command-input');
    inputField.value = ''; // Clear input field

    let currentIndex = 0;

    function displayNextChar() {
        if (currentIndex < text.length) {
            let char = text[currentIndex];
            const charElement = document.createElement('span');
            // Convert space characters to non-breaking spaces
            if (char === ' ') {
                char = '\u00A0'; // Unicode for non-breaking space
            }
            charElement.innerHTML = char;

            if (Math.random() < distortionChance) {
                charElement.style.transform = `rotate(${Math.random() * 4 - 2}deg)`;
            }

            container.appendChild(charElement);

            currentIndex++;
            setTimeout(displayNextChar, delay);
        } else {
            scrollToBottom(); // Ensure scrolling after simulating CRT effect
        }
    }

    displayNextChar();
}

// Function to scroll the terminal window to the bottom
function scrollToBottom() {
    const terminal = document.getElementById('terminal-wrapper');
    terminal.scrollTop = terminal.scrollHeight;
}

// Function to autocomplete command
function autocompleteCommand() {
    const inputElement = document.getElementById('command-input');
    let input = inputElement.value.trim();
    if (input === '') return;

    // Send an AJAX request to the server for autocomplete suggestions
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'auto.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.length > 0) {
                const suggestion = response[0];
                const lastSpaceIndex = input.lastIndexOf(' ');
                const prefix = input.substring(0, lastSpaceIndex + 1);
                const suffix = input.substring(lastSpaceIndex + 1);
                input = prefix + suggestion;
                inputElement.value = input;
                // Set cursor position after the inserted suggestion
                inputElement.setSelectionRange(prefix.length + suggestion.length, prefix.length + suggestion.length);
            }
        }
    };
    xhr.send('input=' + encodeURIComponent(input));
}

// Function to load the saved theme from localStorage
function loadSavedTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme);
    }
}

// Event listener for when the DOM content is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Send a request to the server to get the current directory
    loadSavedTheme();

    // Check if 'boot' command has been sent during the current session
    if (!sessionStorage.getItem('boot')) {

        setTimeout(function() {
            sendCommand('boot', '');
        }, 500);
        
        setTimeout(function() {
            sessionStorage.setItem('boot', true); // Set flag in sessionStorage
            location.reload();
        }, 10000);
    } else {

        setTimeout(function() {
            sendCommand('motd', '');
        }, 500);
    }
});

