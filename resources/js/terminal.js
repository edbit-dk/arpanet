
// Function to load text into terminal one letter at a time
function loadText(text) {
    let currentIndex = 0;
    const preContainer = $('<pre>').css({
        'white-space': 'pre-wrap',   // This preserves newlines and wraps long lines
        'word-wrap': 'break-word'    // Break words if they are too long for the line
    });

    $('#terminal').append(preContainer); // Append the container to the terminal

    function displayNextLetter() {
        if (currentIndex < text.length) {
            preContainer.append(text[currentIndex]);  // Append each letter
            currentIndex++;

            scrollToBottom();  // Ensure terminal auto-scrolls to the bottom
            setTimeout(displayNextLetter, 5);  // Delay between letters
        } else {
            $('#command-input').focus();  // Refocus on the input after all letters are displayed
        }
    }

    displayNextLetter();
}

// Function to simulate CRT effect
function simulateCRT(text, container) {
    const delay = 5;
    const inputField = $('#command-input').val('');

    let currentIndex = 0;
    let currentLine = $('<div>'); // Create a new lin

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

            // Create a span for the word and add it to the current line
            const wordElement = $('<span>').text(word + ' ');

            currentLine.append(wordElement);

            // Check if the word overflows the container
            if (container[0].scrollWidth > container[0].clientWidth) {
                // Remove the word from the current line
                wordElement.remove();

                // Move the word to a new line
                currentLine = $('<div>').append(wordElement);
                container.append(currentLine);
            }

            // Add delay for CRT effect
            setTimeout(displayNextWord, delay);
        } else {
            scrollToBottom();
        }
    }

    displayNextWord();
}

// Function to scroll the terminal window to the bottom
function scrollToBottom() {
    const terminal = document.getElementById('terminal-wrapper');
    terminal.scrollTop = terminal.scrollHeight;
    $('#terminal-wrapper').scrollTop($('#terminal-wrapper')[0].scrollHeight);
}

// Function to clear terminal
function clearTerminal() {
    $('#terminal').empty();
}

// Function to load the saved theme from localStorage
function loadSavedTheme() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        setTheme(savedTheme);
    }
}

// Function to set text and background color
function setTheme(color) {
    const hash = btoa(color);
    $('#theme-color').attr('href', stylesheets + color + '-crt.css?v=' + hash);
    localStorage.setItem('theme', color);
}

// Function to set terminal font
function setTermMode(mode) {
    $("#page").toggleClass(mode);
    localStorage.setItem('mode', mode);
}

// Function to load the saved theme from localStorage
function loadSavedTermMode() {
    const savedTerm = localStorage.getItem('mode');
    if (savedTerm) {
        setTermMode(savedTerm);
    }
}

