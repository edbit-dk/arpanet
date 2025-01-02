
// Function to load text into terminal one letter at a time with 80-character line breaks
function loadText(text) {
    let currentIndex = 0;
    let lineCharCount = 0; // Track character count per line
    const preContainer = $('<pre>').css({
        'white-space': 'pre-wrap',   // Preserve newlines and wrap long lines
        'word-wrap': 'break-word'    // Break words if they are too long for the line
    });

    $('#terminal').append(preContainer); // Append the container to the terminal

    function displayNextLetter() {
        if (currentIndex < text.length) {
            const char = text[currentIndex];

            // Insert a line break if character count exceeds 80 and ensure it doesn’t break mid-word
            if (lineCharCount >= 80 && char !== '\n') {
                const lastChar = preContainer.text().slice(-1);
                if (lastChar !== ' ' && lastChar !== '\n') {
                    // Move back to the last space if possible
                    const textSoFar = preContainer.text();
                    const lastSpaceIndex = textSoFar.lastIndexOf(' ');
                    if (lastSpaceIndex > 0) {
                        preContainer.text(textSoFar.slice(0, lastSpaceIndex) + '\n' + textSoFar.slice(lastSpaceIndex + 1));
                        lineCharCount = textSoFar.slice(lastSpaceIndex + 1).length;
                    } else {
                        preContainer.append('\n');
                        lineCharCount = 0;
                    }
                } else {
                    preContainer.append('\n');
                    lineCharCount = 0;
                }
            }

            preContainer.append(char);
            currentIndex++;

            if (char === '\n') {
                lineCharCount = 0;
            } else {
                lineCharCount++;
            }

            scrollToBottom();
            setTimeout(displayNextLetter, 5);
        } else {
            $('#command-input').focus();
        }
    }

    displayNextLetter();
}

// Function to simulate CRT effect with 80-character line breaks
function simulateCRT(text, container) {
    const delay = 5;
    const inputField = $('#command-input').val('');

    let currentIndex = 0;
    let lineCharCount = 0; // Track character count per line
    let currentLine = $('<div>'); // Create a new line container

    // Add the line container to the DOM
    container.append(currentLine);

    function displayNextWord() {
        if (currentIndex < text.length) {
            let word = '';

            // Read the next word from the text
            while (currentIndex < text.length && text[currentIndex] !== ' ') {
                word += text[currentIndex];
                currentIndex++;
            }
            currentIndex++; // Skip the space

            // Check if adding the word exceeds 80 characters
            if (lineCharCount + word.length > 80) {
                currentLine = $('<div>'); // Create a new line container
                container.append(currentLine);
                lineCharCount = 0; // Reset character count for the new line
            }

            // Create a span for the word and add it to the current line
            const wordElement = $('<span>').text(word + ' ');
            currentLine.append(wordElement);

            lineCharCount += word.length + 1; // Update character count (+1 for the space)

            // Add delay for CRT effect
            setTimeout(displayNextWord, delay);
        } else {
            scrollToBottom();
        }
    }

    displayNextWord();
}

/**
 * Loads text into the terminal display.
 * @param {string} text - Text to display in the terminal.
 */
function loadText(text) {
    const output = $('<div>').addClass('terminal-output').html(text);
    $('#terminal').append(output);
    scrollToBottom();
}

/**
 * Scrolls the terminal view to the bottom.
 */
function scrollToBottom() {
    const terminal = $('#terminal');
    terminal.scrollTop(terminal.prop('scrollHeight'));
}

/**
 * Clears the terminal display.
 */
function clearTerminal() {
    $('#terminal').empty();
}

/**
 * Loads the saved theme from localStorage.
 */
function loadSavedTheme() {
    const theme = localStorage.getItem('theme');
    if (theme) {
        $('#theme-stylesheet').attr('href', `${STYLESHEETS}${theme}.css`);
    }
}

/**
 * Loads the saved terminal mode from localStorage.
 */
function loadSavedTermMode() {
    const termMode = localStorage.getItem('termMode');
    if (termMode) {
        $('body').attr('data-mode', termMode);
    }
}