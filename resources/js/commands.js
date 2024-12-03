
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



// Function to handle user input
function handleUserInput() {
    let input = $('#command-input').val().trim();
    if (input === '') return;

    loadText("cmd: " + input);
    commandHistory.push(input);
    historyIndex = commandHistory.length;
    $('#command-input').val('');

    if (isUsernamePrompt) {
        if (input) {
            if (currentCommand === 'newuser') {
                usernameForNewUser = input;
                loadText("ENTER PASSWORD NOW:");
                isUsernamePrompt = false;
                isPasswordPrompt = true;
                $('#command-input').attr('type', 'password');
            } else if (currentCommand === 'login' || currentCommand === 'logon') {
                usernameForLogon = input;
                loadText("ENTER PASSWORD:");
                isUsernamePrompt = false;
                isPasswordPrompt = true;
                $('#command-input').attr('type', 'password');
            }
            return;
        } else {
            loadText("ERROR: WRONG USERNAME!");
            return;
        }
    }

    if (isPasswordPrompt) {
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
            loadText("ENTER USERNAME NOW:");
            isUsernamePrompt = true;
            currentCommand = 'newuser';
            $('#command-input').attr('type', 'text');
        }
    } else if (command === 'logon' || command === 'login') {
        if (args) {
            usernameForLogon = args;
            loadText("ENTER PASSWORD:");
            isUsernamePrompt = false;
            isPasswordPrompt = true;
            currentCommand = command;
            $('#command-input').attr('type', 'password');
            return;
        } else {
            loadText("ENTER USERNAME:");
            isUsernamePrompt = true;
            currentCommand = command;
            $('#command-input').attr('type', 'text');
            return;
        }
    } else if (['logout', 'logoff', 'reboot', 'dc', 'restart', 'start', 'autoexec', 'exit'].includes(command)) {
        sendCommand(command, args)
            .then(response => {
                if (!response.includes("ERROR")) {
                    setTimeout(function() {
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



/*
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
*/