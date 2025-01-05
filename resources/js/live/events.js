// Event listener for handling keydown events
$('#command-input').keydown(function(e) {
    if (e.key === 'Enter') {
        if (isPasswordPrompt) {
            handlePasswordPrompt(); // Handle password prompt on Enter key press
        } else {
            handleUserInput(); // Handle user input on Enter key press
        }
    } else if (e.key === 'ArrowUp') {
        // Navigate command history on ArrowUp key press
        if (historyIndex > 0) {
            historyIndex--;
            $('#command-input').val(commandHistory[historyIndex]);
        }
    } else if (e.key === 'ArrowDown') {
        // Navigate command history on ArrowDown key press
        if (historyIndex < commandHistory.length - 1) {
            historyIndex++;
            $('#command-input').val(commandHistory[historyIndex]);
        } else {
            // Clear input when reaching the end of history
            historyIndex = commandHistory.length;
            $('#command-input').val('');
        }
    } else if (e.key === 'Tab') {
        e.preventDefault(); // Prevent default tab behavior
        autocomplete(); // Call autocomplete function on Tab key press
    }
});

// Event listener for the play button
document.getElementById('play-button').addEventListener('click', toggleMusic);

