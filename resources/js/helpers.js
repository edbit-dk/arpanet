// Function to handle redirect
function handleRedirect(response, timeout = 1000) {

    if (response.startsWith("Trying")) {
        setTimeout(function() {
            redirectTo('');
        }, timeout);
    }

    if (response.startsWith("EXCACT")) {
        setTimeout(function() {
            redirectTo('');
        }, timeout);
    }

    if (response.startsWith("Password")) {
        setTimeout(function() {
            sessionStorage.setItem('host', true);
            redirectTo('');
        }, timeout);
    }

    if (response.startsWith("Authentication")) {
        setTimeout(function() {
            sessionStorage.setItem('host', true);
            redirectTo('');
        }, timeout);
    }

    if (response.startsWith("SUCCESS") || response.startsWith("Security")) {
        setTimeout(function() {
            redirectTo('');
        }, timeout);
    }
}

// Function to redirect to a specific query string
function redirectTo(url, reload = false) {
    if(reload) {
        return window.location.href = url;
    }
    //clearTerminal();
    sendCommand('welcome', '');
    //$('#connection').load('connection');
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
