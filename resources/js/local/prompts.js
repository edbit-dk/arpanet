/**
 * Handles user input from the command line.
 */
function handleUserInput() {
    let input = $('#command-input').val().trim();
    if (input === '' && !(isPasswordPrompt || isUsernamePrompt)) return;

    loadText(`cmd: ${input}`);
    commandHistory.push(input);
    historyIndex = commandHistory.length;
    $('#command-input').val('');

    if (input === '?') input = 'help';
    if (isUplinkCode(input)) input = `uplink ${input}`;

    switch (input) {
        case 'music start':
            $('#play-button').click();
            return;
        case 'music stop':
            if (audio && !audio.paused) $('#play-button').click();
            return;
        case 'music next':
            if (audio) playNextSong();
            else console.log('Use "music start" first.');
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
            } else if (currentCommand === 'logon') {
                usernameForLogon = input;
                loadText("password:");
                isUsernamePrompt = false;
                isPasswordPrompt = true;
                $('#command-input').attr('type', 'password');
            }
        }
        return;
    }

    if (isPasswordPrompt) {
        userPassword = input;
        $('#command-input').attr('type', 'text');
        isPasswordPrompt = false;
    }

    if (input.includes(' ')) {
        const [cmd, ...args] = input.split(' ');
        sendCommand(cmd, args.join(' '));
    } else {
        sendCommand(input, '');
    }
}

/**
 * Handles the password prompt logic based on the current command.
 */
function handlePasswordPrompt() {
    const input = $('#command-input').val().trim();
    userPassword = input;
    $('#command-input').val('');
    $('#command-input').attr('type', 'text');
    isPasswordPrompt = false;

    if (currentCommand === 'logon') {
        sendCommand(currentCommand, `${usernameForLogon}:${userPassword}`);
    } else if (currentCommand === 'newuser') {
        sendCommand(currentCommand, `${usernameForNewUser}:${userPassword}`);
    }
}

/**
 * Handles responses for password prompts.
 * @param {string} response - Server response after password submission.
 */
function handlePasswordPromptResponse(response) {
    loadText(response);
    if (response.toLowerCase().includes('invalid')) {
        loadText('Try again.');
        isPasswordPrompt = true;
        $('#command-input').attr('type', 'password');
    }
}