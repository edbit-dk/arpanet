// Command input keydown event handler
$('#command-input').on('keydown', function(e) {
    switch (e.key) {
        case 'Enter':
            isPasswordPrompt ? handlePasswordPrompt() : handleUserInput();
            break;
        case 'ArrowUp':
            if (historyIndex > 0) {
                historyIndex--;
                $(this).val(commandHistory[historyIndex]);
            }
            break;
        case 'ArrowDown':
            if (historyIndex < commandHistory.length - 1) {
                historyIndex++;
                $(this).val(commandHistory[historyIndex]);
            } else {
                historyIndex = commandHistory.length;
                $(this).val('');
            }
            break;
        case 'Tab':
            e.preventDefault();
            autocomplete();
            break;
    }
});

// Event listener for the play button
document.getElementById('play-button').addEventListener('click', toggleMusic);