
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


// Function to append command to terminal window
function appendCommand(command) {
    const commandElement = $('<div>').addClass('command-prompt').html(command);
    $('#terminal').append(commandElement);
    scrollToBottom(); 
}



// Function to handle user input
function handleUserInput() {
    const input = $('#command-input').val().trim();

    if (input === '') return; // Ignore empty input

    // Append the command to the terminal and add it to history
    loadText("cmd:>" + input);
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