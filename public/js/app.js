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
let currentSongIndex = 0;
let audio;

// Event listener for when the DOM content is loaded
$(document).ready(function() {
    // Load the saved theme when the document is ready
    loadSavedTheme();

    // Load the saved term when the document is ready
    loadSavedTermMode();

    //Check commands available
    autoHelp();

    // Check if 'boot' command has been sent during the current session
    if (!localStorage.getItem('boot')) {

        setTimeout(function() {
            sendCommand('boot', ''); // Send 'boot' command
        }, 500);
        
        setTimeout(function() {
            localStorage.setItem('boot', true); // Set 'boot' flag in sessionStorage
            clearTerminal();
            sendCommand('welcome', '');
        }, 10000);
    } else {

        setTimeout(function() {
            sendCommand('welcome', ''); // Send 'welcome' command if boot has been set
            $('#connection').load('connection');
        }, 500);
    }
});
// Event listener for handling keydown events
$('#command-input').keydown(function(e) {
    if (e.key === 'Enter') {
        e.preventDefault(); // Prevent default tab behavior
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

// Event listener for the play button
document.getElementById('play-button').addEventListener('click', toggleMusic);

// Function to handle redirect
function handleRedirect(response) {

    if (response.startsWith("Trying")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 1000);
    }

    if (response.startsWith("EXCACT")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 1000);
    }

    if (response.startsWith("Password")) {
        setTimeout(function() {
            sessionStorage.setItem('host', true);
            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 1000);
    }

    if (response.startsWith("Authentication")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 1000);
    }

    if (response.startsWith("SUCCESS") || response.startsWith("Security")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 1000);
    }
}

// Function to redirect to a specific query string
function redirectTo(url, reload = false) {
    if(reload) {
        return window.location.href = url;
    }
    clearTerminal();
    sendCommand('welcome', '');
    $('#connection').load('connection');
}

// Function to validate the string pattern
function isUplinkCode(input) {
    // Check if the input is 27 characters long and matches the alphanumeric pattern (allowing dashes)
    const pattern = /^[A-Za-z0-9\-]{27}$/;

    // Test the input against the pattern
    return pattern.test(input);
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
// Function to handle user input
function handleUserInput() {
    let input = $('#command-input').val().trim();
    if (input === '' && !(isPasswordPrompt || isUsernamePrompt)) return;
    // Prevent empty commands unless it's a password prompt

    // Normal command handling
    loadText("cmd: " + input);
    commandHistory.push(input);
    localStorage.setItem('history',commandHistory);
    historyIndex = commandHistory.length;
    localStorage.setItem('index',historyIndex);
    $('#command-input').val('');

    // Check if the input is "?" and change it to "help"
    if (input === '?') {
        input = 'help';
    }

    if (isUplinkCode(input)) {
        input = 'uplink ' + input;
    }

    // Handle "music start", "music stop", and "music next" commands
    if (input === 'music start') {
        console.log('music start');
        document.getElementById('play-button').click(); // Simulate a button click to start music
        $('#command-input').val('');
        return;
    }

    if (input === 'music stop') {
        console.log('music stop');
        if (audio && !audio.paused) {
            document.getElementById('play-button').click(); // Simulate a button click to stop music
        }
        $('#command-input').val('');
        return;
    }

    if (input === 'music next') {
        console.log('music next');
        if (audio) {
            playNextSong(); // Call the function to skip to the next song
        } else {
            console.log('Use "music start" first.');
        }
        $('#command-input').val('');
        return;
    }

    if (isUsernamePrompt) {
        if (input) {
            if (currentCommand === 'newuser') {
                usernameForNewUser = input;
                loadText("Password:");
                isUsernamePrompt = false;
                isPasswordPrompt = true;
                $('#command-input').attr('type', 'password');
            } else if (currentCommand === 'login' || currentCommand === 'logon') {
                usernameForLogon = input;
                loadText("Password:");
                isUsernamePrompt = false;
                isPasswordPrompt = true;
                $('#command-input').attr('type', 'password');
            }
            return;
        } else {
            loadText("ERROR: Wrong Username!");
            return;
        }
    }

    if (isPasswordPrompt) {
        // Allow an empty password
        handlePasswordPrompt();
        return;
    }

    const parts = input.split(' ');
    const command = parts[0].toLowerCase(); // Only the command is transformed to lowercase
    const args = parts.slice(1).join(' ');

    if(command === 'term') {
        setTermMode(args);
        return;
    }

    if (['newuser', 'logon', 'login'].includes(command) && !localStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required.");
        return;
    }

    if (['logon', 'login', 'newuser'].includes(command) && sessionStorage.getItem('auth') && !sessionStorage.getItem('host')) {
        loadText("ERROR: Logout Required.");
        return;
    }

    if (command === 'clear' || command === 'cls') {
        clearTerminal();
    } else if (command === 'uplink') {
        localStorage.setItem('uplink', true);
        sendCommand(command, args);
    } else if (command === 'newuser') {
        if (args) {
            handleNewUser(args);
        } else {
            loadText("username:");
            isUsernamePrompt = true;
            currentCommand = 'newuser';
            $('#command-input').attr('type', 'text');
        }
    } else if (command === 'logon' || command === 'login') {
        if (args) {
            usernameForLogon = args;
            loadText("Password:");
            isUsernamePrompt = false;
            isPasswordPrompt = true;
            currentCommand = command;
            $('#command-input').attr('type', 'password');
            return;
        } else {
            loadText("login:");
            isUsernamePrompt = true;
            currentCommand = command;
            $('#command-input').attr('type', 'text');
            return;
        }
    } else if (['logout', 'close', 'logoff', 'quit', 'dc', 'restart', 'exit', 'reboot', 'halt', 'halt restart', 'restart'].includes(command)) {
        sendCommand(command, args)
            .then(response => {
                if (!response.includes("ERROR")) {
                    setTimeout(function () {
                        if(sessionStorage.getItem('host')) {
                            sessionStorage.removeItem('host');
                        }

                        if(['boot', 'reboot', 'halt', 'halt restart', 'restart'].includes(command)) {
                            localStorage.removeItem('boot');
                        }
                        redirectTo('', true);
                    }, 1000);
                }
            })
            .catch(err => {
                console.error("Command failed", err);
            });
    } else if (command === 'color') {
        setTheme(args);
    } else {
        sendCommand(command, args);
    }
}// Function to send command to server
function sendCommand(command, data, queryString = '') {
    const query = window.location.search;
    const route = command.split(" ")[0];

    return new Promise((resolve, reject) => {
        $.ajax({
            type: 'GET',
            url: route.toLowerCase() + queryString,
            data: {
                data: data,
                query: query
            },
            success: function(response) {
                loadSavedTheme();
                
                if(sessionStorage.getItem('host')) {
                    $('#connection').load('connection');
                }

                if (isPasswordPrompt) {
                    handlePasswordPromptResponse(response); // Handle password prompt response
                } else {
                    loadText(response); // Load response text into terminal
                    handleRedirect(response); // Handle redirect if needed
                }
                resolve(response); // Resolve the promise with the response
            },
            error: function(err) {
                reject(err); // Reject the promise in case of an error
            }
        });
    });
}

// Function to append command to terminal window
function appendCommand(command) {
    const commandElement = $('<div>').addClass('command-prompt').html(command);
    $('#terminal').append(commandElement);
    scrollToBottom(); 
}

// Fetch commands from the server based on the user's status
function autoHelp() {
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


// Function to handle the LOGON/LOGIN command
function handleLogon(username) {
    if (!sessionStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required.");
        return;
    }

    if (!usernameForLogon && !username) {
        loadText("username:");
        isUsernamePrompt = true;
        $('#command-input').attr('type', 'text'); // Switch input to text for username
        return;
    }

    if (isPasswordPrompt) return; // Already prompting for password, do nothing
    isPasswordPrompt = true;
    usernameForLogon = username;
    loadText("password:");
    $('#command-input').attr('type', 'password'); // Change input to password
}

// Function to handle the NEWUSER command
function handleNewUser(username) {
    if (!sessionStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required.");
        return;
    }

    if (!username) {
        // This shouldn't happen since args should be checked in handleUserInput()
        loadText("ERROR: Username Required.");
        return;
    } else {
        // Assign the provided username
        usernameForNewUser = username;
    }

    // Proceed to password prompt
    isPasswordPrompt = true;
    loadText("password:");
    $('#command-input').attr('type', 'password');
}

// Function to handle password prompt
function handlePasswordPrompt() {
    let password = $('#command-input').val(); // Capture the password input, allow it to be empty
    if (!password) password = ""; // Explicitly set to an empty string if blank
    userPassword = password;

    // Determine the current command and send the appropriate request
    if (currentCommand === 'logon' || currentCommand === 'login') {
        sendCommand(currentCommand, usernameForLogon + ' ' + userPassword);
        usernameForLogon = ''; // Clear the username for logon
    } else if (currentCommand === 'newuser') {
        sendCommand('newuser', usernameForNewUser + ' ' + userPassword);
        usernameForNewUser = ''; // Clear the username for new user creation
    }

    // Reset prompt state and input type
    isPasswordPrompt = false;
    $('#command-input').attr('type', 'text').val('');
}

// Function to handle password prompt response
function handlePasswordPromptResponse(response) {
    if (response.startsWith("ERROR") || response.startsWith("WARNING")) {
        loadText(response);
        isPasswordPrompt = false;
        $('#command-input').attr('type', 'text');
    } else if (response.startsWith("Authentication Successful") || response.startsWith("Password Verified")) {
        loadText(response);
        setTimeout(function() {
            sessionStorage.setItem('auth', true);
            clearTerminal();
            sendCommand('welcome', '');
        }, 2500);
    } else {
        if (usernameForLogon) {
            sendCommand('logon', usernameForLogon + ' ' + (userPassword || ""));
        } else if (usernameForNewUser) {
            sendCommand('newuser', usernameForNewUser + ' ' + (userPassword || ""));
        }
    }
    $('#command-input').val('');
}
// Function to load text into terminal one letter at a time with 80-character line breaks
function loadText(text) {
    let currentIndex = 0;
    let lineCharCount = 0; // Track character count per line
    const preContainer = $('<pre>').css({
        'white-space': 'pre-wrap',   // Preserve newlines and wrap long lines
        'word-wrap': 'break-word'    // Break words if they are too long for the line
    });

    $('#terminal').append(preContainer); // Append the container to the terminal

    function displayNextLetter() {
        if (currentIndex < text.length) {
            const char = text[currentIndex];

            // Insert a line break if character count exceeds 80 and ensure it doesn’t break mid-word
            if (lineCharCount >= 80 && char !== '\n') {
                const lastChar = preContainer.text().slice(-1);
                if (lastChar !== ' ' && lastChar !== '\n') {
                    // Move back to the last space if possible
                    const textSoFar = preContainer.text();
                    const lastSpaceIndex = textSoFar.lastIndexOf(' ');
                    if (lastSpaceIndex > 0) {
                        preContainer.text(textSoFar.slice(0, lastSpaceIndex) + '\n' + textSoFar.slice(lastSpaceIndex + 1));
                        lineCharCount = textSoFar.slice(lastSpaceIndex + 1).length;
                    } else {
                        preContainer.append('\n');
                        lineCharCount = 0;
                    }
                } else {
                    preContainer.append('\n');
                    lineCharCount = 0;
                }
            }

            preContainer.append(char);
            currentIndex++;

            if (char === '\n') {
                lineCharCount = 0;
            } else {
                lineCharCount++;
            }

            scrollToBottom();
            setTimeout(displayNextLetter, 1);
        } else {
            $('#command-input').focus();
        }
    }

    displayNextLetter();
}

// Function to simulate CRT effect with 80-character line breaks
function simulateCRT(text, container) {
    const delay = 1;
    const inputField = $('#command-input').val('');

    let currentIndex = 0;
    let lineCharCount = 0; // Track character count per line
    let currentLine = $('<div>'); // Create a new line container

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

            // Check if adding the word exceeds 80 characters
            if (lineCharCount + word.length > 80) {
                currentLine = $('<div>'); // Create a new line container
                container.append(currentLine);
                lineCharCount = 0; // Reset character count for the new line
            }

            // Create a span for the word and add it to the current line
            const wordElement = $('<span>').text(word + ' ');
            currentLine.append(wordElement);

            lineCharCount += word.length + 1; // Update character count (+1 for the space)

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

// Function to clear terminal
function clearTerminal() {
    $('#terminal').empty();
}

// Function to load the saved theme from localStorage
function loadSavedTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme);
    }
}

// Function to set text and background color
function setTheme(color) {
  
    const colors = {
        green: "#0f0",
        white: "#EAF7F9",
        yellow: "#DBC853",
        blue: "#0CD7CF",
    };

    const defaultColor = "green";

    // Validate the color and apply it, defaulting to green if invalid
    const themeColor = colors[color] || colors[defaultColor];

    $('*').css('color', themeColor);

    localStorage.setItem('theme', themeColor);
}

// Function to set terminal font
function setTermMode(mode) {
    $("#page").attr('class', mode);
    localStorage.setItem('term', mode);
    sendCommand('term', mode);
}

// Function to load the saved theme from localStorage
function loadSavedTermMode() {
    const savedTerm = localStorage.getItem('term');
    if (savedTerm) {
        setTermMode(savedTerm);
    }
}

// Function to create the audio element only after user interaction
function initializeAudio() {
  if (!audio) {
    audio = new Audio(playlist[currentSongIndex]);
    audio.loop = false; // Disable looping for queuing purposes

    audio.addEventListener('ended', handleAudioEnded);
    console.log('Audio element initialized.');
  }
}

// Function to play the next song
function playNextSong() {
  if (playlist.length === 0) {
    console.log('Playlist is empty.');
    return;
  }

  currentSongIndex = (currentSongIndex + 1) % playlist.length; // Move to the next song, wrap around if needed
  audio.src = playlist[currentSongIndex];
  audio.play()
    .then(() => {
      console.log(`Playing next song: ${playlist[currentSongIndex]}`);
      document.getElementById('play-button').textContent = 'MUSIC STOP';
    })
    .catch(error => {
      console.error('Playback failed:', error);
      alert('Audio playback failed. Please try again or interact with the page.');
    });
}

// Function to handle when the current song ends
function handleAudioEnded() {
  playNextSong(); // Automatically play the next song when the current one ends
}

// Function to toggle play/pause for music
function toggleMusic() {
  initializeAudio();

  if (audio.paused) {
    audio.play().then(() => {
      console.log('Audio started playing.');
      document.getElementById('play-button').textContent = 'MUSIC STOP'; // Update button text
    }).catch(error => {
      console.error('Playback failed:', error);
      alert('Audio playback failed. Please try again or interact with the page.');
    });
  } else {
    audio.pause();
    console.log('Audio paused.');
    document.getElementById('play-button').textContent = 'MUSIC PLAY'; // Update button text
  }
}


