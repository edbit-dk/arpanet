// Function to handle redirect
function handleRedirect(response) {

    if (response.startsWith("Trying")) {
        setTimeout(function() {
            loadText("SUCCESS: Connecting to Host");

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

        }, 2000);
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

    if (response.startsWith("Security")) {
            setTimeout(function() {

                loadText("SUCCESS: WELCOME to ARPANET");

                setTimeout(function() {
                    redirectTo('');
                }, 1000);

            }, 1000);
    }
}

// Function to redirect to a specific query string
function redirectTo(url) {
    setTimeout(function() {
        window.location.href = url;
    }, 2000);
}