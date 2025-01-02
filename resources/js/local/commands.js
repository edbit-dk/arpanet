/**
 * Sends a command to the server and processes the response.
 * @param {string} command - Command to send.
 * @param {string} data - Data to accompany the command.
 * @param {string} [queryString=''] - Optional query string.
 * @returns {Promise} - Promise resolving with the server response.
 */
function sendCommand(command, data, queryString = '') {
    const query = window.location.search;
    const route = command.split(" ")[0].toLowerCase();

    return $.ajax({
        type: 'GET',
        url: `${route}${queryString}`,
        data: { data, query },
        success(response) {
            if (isPasswordPrompt) {
                handlePasswordPromptResponse(response);
            } else {
                loadText(response);
                handleRedirect(response);
            }
        },
        error(err) {
            console.error('Command failed', err);
        }
    });
}

/**
 * Appends the entered command to the terminal display.
 * @param {string} command - Command to display.
 */
function appendCommand(command) {
    const commandElement = $('<div>').addClass('command-prompt').html(command);
    $('#terminal').append(commandElement);
    scrollToBottom();
}

/**
 * Fetches and lists available commands for autocomplete.
 */
function listCommands() {
    fetch('help?data=auto')
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                commands = data.filter(item => typeof item === 'string');
            } else {
                console.error('Invalid commands data:', data);
            }
        })
        .catch(error => console.error('Error fetching commands:', error));
}

/**
 * Autocompletes the current command input based on available commands.
 */
function autocomplete() {
    const inputField = $('#command-input');
    const currentText = inputField.val().trim();
    const matches = commands.filter(cmd => cmd.startsWith(currentText));

    if (matches.length === 1) {
        inputField.val(matches[0]);
    } else if (matches.length > 1) {
        const commonPrefix = findCommonPrefix(matches);
        if (commonPrefix.length > currentText.length) {
            inputField.val(commonPrefix);
        } else {
            loadText(matches.join(' '));
        }
    } else {
        loadText('');
    }
}
