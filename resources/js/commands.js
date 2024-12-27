
// Function to send command to server
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

// Function to validate the string pattern
function isUplinkCode(input) {
    // Check if the input is 27 characters long and matches the alphanumeric pattern (allowing dashes)
    const pattern = /^[A-Za-z0-9\-]{27}$/;

    // Test the input against the pattern
    return pattern.test(input);
}

// Function to handle user input
function handleUserInput() {
    let input = $('#command-input').val().trim();
    if (input === '' && !(isPasswordPrompt || isUsernamePrompt)) return;
    // Prevent empty commands unless it's a password prompt

    // Normal command handling
    loadText("cmd: " + input);
    commandHistory.push(input);
    historyIndex = commandHistory.length;
    $('#command-input').val('');

    // Check if the input is "?" and change it to "help"
    if (input === '?') {
        input = 'help';
    }

    if (input === 'mem' || input === 'debug') {
        clearTerminal();
        return;
    }

    if (isUplinkCode(input)) {
        input = 'uplink ' + input;
    }

    // Handle "music start", "music stop", and "music next" commands
    if (input === 'music start') {
        console.log('Command received: music start');
        document.getElementById('play-button').click(); // Simulate a button click to start music
        $('#command-input').val('');
        return;
    }

    if (input === 'music stop') {
        console.log('Command received: music stop');
        if (audio && !audio.paused) {
            document.getElementById('play-button').click(); // Simulate a button click to stop music
        }
        $('#command-input').val('');
        return;
    }

    if (input === 'music next') {
        console.log('Command received: music next');
        if (audio) {
            playNextSong(); // Call the function to skip to the next song
        } else {
            console.log('Audio not initialized. Use "music start" first.');
        }
        $('#command-input').val('');
        return;
    }

    if (isUsernamePrompt) {
        if (input) {
            if (currentCommand === 'newuser') {
                usernameForNewUser = input;
                loadText("password:");
                isUsernamePrompt = false;
                isPasswordPrompt = true;
                $('#command-input').attr('type', 'password');
            } else if (currentCommand === 'login' || currentCommand === 'logon') {
                usernameForLogon = input;
                loadText("password:");
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

    if (['newuser', 'logon', 'login'].includes(command) && !sessionStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required.");
        return;
    }

    if (command === 'clear' || command === 'cls') {
        clearTerminal();
    } else if (command === 'uplink') {
        sessionStorage.setItem('uplink', true);
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
            loadText("password:");
            isUsernamePrompt = false;
            isPasswordPrompt = true;
            currentCommand = command;
            $('#command-input').attr('type', 'password');
            return;
        } else {
            loadText("username:");
            isUsernamePrompt = true;
            currentCommand = command;
            $('#command-input').attr('type', 'text');
            return;
        }
    } else if (['logout', 'logoff', 'reboot', 'dc', 'restart', 'start', 'exit'].includes(command)) {
        sendCommand(command, args)
            .then(response => {
                if (!response.includes("ERROR")) {
                    setTimeout(function () {
                        sessionStorage.setItem('uplink', false);
                        redirectTo('');
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
}


