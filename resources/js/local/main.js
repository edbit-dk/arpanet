// Define paths for public resources
const PATH_PUBLIC = 'public/';
const STYLESHEETS = `${PATH_PUBLIC}css/`;

// Initialize music variables
let currentSongIndex = 0;
let audio;

// Initialize command history and indices
let commandHistory = [];
let historyIndex = -1;

// Initialize terminal state variables
let currentDirectory = '';
let isPasswordPrompt = false;
let userPassword = '';
let usernameForLogon = '';
let usernameForNewUser = '';
let isUsernamePrompt = false;
let currentCommand = '';
let commands = [];

// Document ready function
$(document).ready(function() {
    loadSavedTheme();
    loadSavedTermMode();
    listCommands();

    if (!sessionStorage.getItem('boot')) {
        setTimeout(() => sendCommand('boot', ''), 500);
        setTimeout(() => {
            sessionStorage.setItem('boot', true);
            clearTerminal();
            sendCommand('welcome', '');
        }, 10000);
    } else {
        setTimeout(() => sendCommand('welcome', ''), 500);
    }
});