// Array to store command history
let commandHistory = [];
let historyIndex = -1;
let currentDirectory = ''; // Variable to store the current directory
let isPasswordPrompt = false; // Flag to track if password prompt is active
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
function sendCommand(command, data) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', 'server.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = xhr.responseText;
            if (isPasswordPrompt) {
                handlePasswordPromptResponse(response); // Handle password prompt response
            } else {
                loadText(response); // Load response text into terminal
            }
        }
    };
    xhr.send('command=' + encodeURIComponent(command) + '&data=' + encodeURIComponent(data));
}

// Event listener for handling Enter key press
document.getElementById('command-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        if (isPasswordPrompt) {
            handlePasswordPrompt(); // Handle password prompt on Enter key press
        } else {
            handleUserInput(); // Handle user input on Enter key press
        }
    }
});

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
    if (command === 'clear') {
        clearTerminal(); // Clear the terminal
    } else if (command === 'login') {
        clearTerminal(); // Clear the terminal
        handleLogon(args); // Handle logon command
    } else if (command === 'exit') {
        sendCommand(command, args); // Otherwise, send the command to the server
        clearTerminal(); // Clear the terminal
    } else if (command === 'register') {
        clearTerminal(); // Clear the terminal
        handleNewUser(args); // Handle new user creation
    } else {
        sendCommand(command, args); // Otherwise, send the command to the server
    }
}

// Function to handle creating a new user
function handleNewUser(username) {
    if (!username) {
        appendCommand("Please provide username. ");
        return;
    }
    isPasswordPrompt = true;
    document.getElementById('command-input').type = 'password'; // Change input type to password
    usernameForNewUser = username; // Store the username for new user
    sendCommand('register', username);
}

// Function to handle the LOGON command
function handleLogon(username) {
    if (!username) {
        appendCommand("Please provide username. ");
        return;
    }
    isPasswordPrompt = true;
    document.getElementById('command-input').type = 'password'; // Change input type to password
    usernameForLogon = username; // Store the username for logon
    sendCommand('login', username);
}

// Function to handle password prompt
function handlePasswordPrompt() {
    const password = document.getElementById('command-input').value.trim();
    if (password === '') return; // Ignore empty password

    isPasswordPrompt = false;
    document.getElementById('command-input').type = 'text'; // Change input type back to text
    document.getElementById('command-input').value = '';
    clearTerminal(); // Clear the terminal
    if (usernameForNewUser) {
        sendCommand('register', usernameForNewUser + ' ' + password);
    } else {
        sendCommand('login', usernameForLogon + ' ' + password);
    }
}

// Function to handle response for password prompt
function handlePasswordPromptResponse(response) {
    appendCommand(response); // Display response in terminal
    if (response.startsWith("Welcome")) {
        const username = response.split(" ")[1];
        document.getElementById('user').textContent = username;
    }
}

// Function to append command to terminal window
function appendCommand(command) {
    const terminal = document.getElementById('terminal');
    const commandElement = document.createElement('div');
    commandElement.classList.add('command-prompt'); // Add command prompt class
    commandElement.textContent = command;
    terminal.appendChild(commandElement);
}

// Function to clear terminal
function clearTerminal() {
    document.getElementById('terminal').innerHTML = '';
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
                setTimeout(displayNextLine, 500); // Adjust delay as needed
            }
        }
    }

    displayNextLine(); // Start displaying lines
}

// Function to simulate CRT effect
function simulateCRT(text, container) {
    const delay = 1; // Delay between each character in milliseconds
    const distortionChance = 0.5; // Chance of random distortion per character

    let currentIndex = 0;

    function displayNextChar() {
        if (currentIndex < text.length) {
            const char = text[currentIndex];
            const charElement = document.createElement('span');
            charElement.textContent = char;

            if (Math.random() < distortionChance) {
                charElement.style.transform = `rotate(${Math.random() * 4 - 2}deg)`;
            }

            container.appendChild(charElement);

            currentIndex++;
            setTimeout(displayNextChar, delay);
        }
    }

    displayNextChar();
}

// Function to scroll the terminal window to the bottom
function scrollToBottom() {
    const terminal = document.getElementById('terminal');
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

// Event listener for when the DOM content is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Send a request to the server to get the current directory
    sendCommand('welcome', '');
});
