// Function to handle redirect
function handleRedirect(response) {

    if (response.startsWith("Contacting")) {
        setTimeout(function() {
            loadText("Accessing Mainframe...");

            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 2000);
    }

    if (response.startsWith("EXCACT")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 2500);

        }, 2000);
    }

    if (response.startsWith("Password")) {
        setTimeout(function() {
            setTimeout(function() {
                redirectTo('');
            }, 1000);

        }, 2000);
    }

    if (response.startsWith("Security")) {
            setTimeout(function() {

                loadText("SUCCESS: Welcome to ARPANET");

                setTimeout(function() {
                    redirectTo('');
                }, 2000);

            }, 2000);
    }
}

// Function to redirect to a specific query string
function redirectTo(url) {
    setTimeout(function() {
        window.location.href = url;
    }, 2500);
}