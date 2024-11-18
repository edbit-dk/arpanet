let currentSongIndex = 0;
let audio;

// Function to create the audio element only after user interaction
function initializeAudio() {
  if (!audio) {
    audio = new Audio(playlist[currentSongIndex]);
    audio.loop = false; // Disable looping for queuing purposes

    audio.addEventListener('ended', handleAudioEnded);
    console.log('Audio element initialized.');
  }
}

// Function to handle play/pause toggle
function toggleMusic() {
  if (!audio) {
    console.log('Initializing audio for the first time.');
    initializeAudio();
  }

  if (audio.paused) {
    audio.play().then(() => {
      console.log('Audio started playing.');
      document.getElementById('play-button').textContent = 'STOP MUSIC'; // Update button text
    }).catch(error => {
      console.error('Playback failed:', error);
      alert('Audio playback failed. Please try again or interact with the page.');
    });
  } else {
    audio.pause();
    console.log('Audio paused.');
    document.getElementById('play-button').textContent = 'PLAY MUSIC'; // Update button text
  }
}

// Event listener for the play button (click to play/pause)
document.getElementById('play-button').addEventListener('click', () => {
  initializeAudio();
  toggleMusic();
});

// Function to handle when the current song ends
function handleAudioEnded() {
  currentSongIndex++;
  if (currentSongIndex < playlist.length) {
    audio.src = playlist[currentSongIndex];
    audio.play().catch(error => {
      console.warn('Playback failed:', error);
      alert('Audio playback failed for the next track. Please try again.');
    });
  } else {
    currentSongIndex = 0;
    audio.src = playlist[currentSongIndex];
    audio.play().catch(error => {
      console.warn('Playback failed:', error);
    });
  }
}

/*
// Ensure user interaction is recorded
document.addEventListener('click', () => {
  if (!audio || audio.paused) {
    console.log('Attempting to play audio after user interaction.');
    initializeAudio();
    audio.play().catch(error => {
      console.warn('Autoplay blocked or failed:', error);
      alert('Autoplay is blocked by your browser. Please press play to start the music.');
    });
  }
}, { once: true }); // This listener runs once to ensure interaction is captured
*/