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
        sessionStorage.setItem('uplink', true);
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

    const parts = input.split(' ');
    const command = parts[0].toLowerCase(); // Only the command is transformed to lowercase
    const args = parts.slice(1).join(' ');

    if(command === 'term') {
        setTermMode(args);
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
            loadText("WRONG USERNAME.");
            return;
        }
    }

    if (isPasswordPrompt) {
        // Allow an empty password
        handlePasswordPrompt();
        return;
    }

    if (['newuser', 'logon', 'login'].includes(command) && !sessionStorage.getItem('uplink')) {
        loadText("UPLINK REQUIRED");
        return;
    }

    if (['logon', 'login', 'newuser'].includes(command) && sessionStorage.getItem('auth') && !sessionStorage.getItem('host')) {
        loadText("LOGOUT REQUIRED.");
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
                        redirectTo('', false);
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