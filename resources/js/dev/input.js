// Function to handle user input
function handleUserInput() {
    let input = $('#command-input').val().trim();
    if (isInputInvalid(input)) return;

    loadText(`cmd: ${input}`);
    commandHistory.push(input);
    historyIndex = commandHistory.length;
    $('#command-input').val('');

    input = preprocessInput(input);

    if (handleMusicCommands(input) || handlePrompts(input)) return;

    const [command, ...argsArray] = input.split(' ');
    const args = argsArray.join(' ');
    const normalizedCommand = command.toLowerCase();

    if (handleSpecialCommands(normalizedCommand, args)) return;

    if(['dc', 'exit'].includes(normalizedCommand)) {
        setTimeout(() => {
            redirectTo('');
        }, 1500);
    }

    if (['logout', 'logoff', 'reboot', 'restart', 'start'].includes(normalizedCommand)) {
        handleLogout(normalizedCommand, args);
    } else if (normalizedCommand === 'color') {
        setTheme(args);
    } else {
        sendCommand(normalizedCommand, args);
    }
}

// Helper function to validate input
function isInputInvalid(input) {
    return input === '' && !(isPasswordPrompt || isUsernamePrompt);
}

// Preprocess input for special cases
function preprocessInput(input) {
    if (input === '?') return 'help';
    if (isUplinkCode(input)) return `uplink ${input}`;
    return input;
}

// Handle music-related commands
function handleMusicCommands(input) {
    switch (input) {
        case 'music start':
            console.log('music start');
            document.getElementById('play-button').click();
            return true;
        case 'music stop':
            console.log('music stop');
            if (audio && !audio.paused) {
                document.getElementById('play-button').click();
            }
            return true;
        case 'music next':
            console.log('music next');
            if (audio) {
                playNextSong();
            } else {
                console.log('Use "music start" first.');
            }
            return true;
        default:
            return false;
    }
}

// Handle username and password prompts
function handlePrompts(input) {
    if (isUsernamePrompt) {
        return handleUsernamePrompt(input);
    }

    if (isPasswordPrompt) {
        handlePasswordPrompt();
        return true;
    }

    return false;
}

function handleUsernamePrompt(input) {
    if (!input) {
        loadText("ERROR: Wrong Username!");
        return true;
    }

    if (currentCommand === 'newuser') {
        usernameForNewUser = input;
        prepareForPasswordPrompt();
    } else if (['login', 'logon'].includes(currentCommand)) {
        usernameForLogon = input;
        prepareForPasswordPrompt();
    }

    return true;
}

function prepareForPasswordPrompt() {
    loadText("password:");
    isUsernamePrompt = false;
    isPasswordPrompt = true;
    $('#command-input').attr('type', 'password');
}

// Handle special commands and validation
function handleSpecialCommands(command, args) {
    if (command === 'mode') {
        setTermMode(args);
        return true;
    }

    if (['newuser', 'logon', 'login'].includes(command)) {
        return handleAuthCommands(command, args);
    }

    if (command === 'clear' || command === 'cls') {
        clearTerminal();
        return true;
    }

    if (command === 'uplink') {
        sessionStorage.setItem('uplink', true);
        sendCommand(command, args);
        return true;
    }

    return false;
}

function handleAuthCommands(command, args) {
    if (!sessionStorage.getItem('uplink')) {
        loadText("ERROR: Uplink Required.");
        return true;
    }

    if (sessionStorage.getItem('auth') && !sessionStorage.getItem('host')) {
        loadText("ERROR: Logout Required.");
        return true;
    }

    if (command === 'newuser') {
        return handleNewUserCommand(args);
    }

    if (['logon', 'login'].includes(command)) {
        return handleLoginCommand(command, args);
    }

    return false;
}

function handleNewUserCommand(args) {
    if (args) {
        handleNewUser(args);
    } else {
        loadText("username:");
        isUsernamePrompt = true;
        currentCommand = 'newuser';
        $('#command-input').attr('type', 'text');
    }
    return true;
}

function handleLoginCommand(command, args) {
    if (args) {
        usernameForLogon = args;
        prepareForPasswordPrompt();
    } else {
        loadText("username:");
        isUsernamePrompt = true;
        currentCommand = command;
        $('#command-input').attr('type', 'text');
    }
    return true;
}

// Handle logout and related commands
function handleLogout(command, args) {
    sendCommand(command, args)
        .then(response => {
            if (!response.includes("ERROR")) {
                setTimeout(() => {
                    sessionStorage.removeItem('auth');
                    sessionStorage.removeItem('uplink');
                    redirectTo('');
                }, 1000);
            }
        })
        .catch(err => console.error("Command failed", err));
}
