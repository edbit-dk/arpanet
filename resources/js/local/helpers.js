/**
 * Checks if the input matches the Uplink code pattern.
 * @param {string} input - Input to validate.
 * @returns {boolean} - True if input is a valid Uplink code, else false.
 */
function isUplinkCode(input) {
    const pattern = /^[A-Za-z0-9\-]{27}$/;
    return pattern.test(input);
}

/**
 * Handles redirect commands sent from the server response.
 * @param {string} response - Server response containing potential redirect instructions.
 */
function redirect(response) {
    const redirectMatch = response.match(/REDIRECT:([^\s]+)/i);
    if (redirectMatch) {
        window.location.href = redirectMatch[1];
    }
}

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

    if (response.startsWith("Authentication")) {
            setTimeout(function() {

                loadText("Welcome to ARPANET");

                setTimeout(function() {
                    sendCommand('scan', '');
                }, 2000);

            }, 1000);
    }
}

// Function to redirect to a specific query string
function redirectTo(url) {
    // window.location.href = url;
  clearTerminal();
  sendCommand('welcome', '');
 // $('#user').load('connection');
}

/**
 * Finds the common prefix among an array of strings.
 * @param {string[]} strings - Array of strings to compare.
 * @returns {string} - Common prefix.
 */
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