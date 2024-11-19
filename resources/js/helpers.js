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

    if (response.startsWith("SUCCESS")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 1000);
    }

    if (response.includes("TERMINAL LOCKED")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 2000);

        }, 1000);
}

    if (response.startsWith("Authentication")) {
            setTimeout(function() {

                loadText("Welcome to ARPANET");

                setTimeout(function() {
                    redirectTo('');
                }, 2000);

            }, 1000);
    }
}

// Function to redirect to a specific query string
function redirectTo(url) {
      // window.location.href = url;
    clearTerminal();
    sendCommand('welcome', '');
    $('#user').load('connection');
}