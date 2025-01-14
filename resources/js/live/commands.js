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
                loadSavedTheme();
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
function listCommands() {
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


