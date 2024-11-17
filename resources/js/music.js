let currentSongIndex = 0;
const audio = new Audio(playlist[currentSongIndex]);
audio.loop = false; // Disable looping for queuing purposes

// Check if the user has previously interacted from localStorage
let userInteracted = localStorage.getItem('music') === 'true';

// Function to toggle play/pause (for the button)
function toggleMusic() {
  if (audio.paused) {
    // If audio is paused, start playing
    audio.play().then(() => {
      console.log('Audio started playing.');
      document.getElementById('play-button').textContent = 'STOP MUSIC'; // Update button text
    }).catch(error => {
      console.error('Playback failed:', error);
      alert('Audio playback failed. Please try again or interact with the page.');
    });
  } else {
    // If audio is playing, pause it
    audio.pause();
    console.log('Audio paused.');
    document.getElementById('play-button').textContent = 'PLAY MUSIC'; // Update button text
  }
}

// Event listener for the play button (click to play/pause)
document.getElementById('play-button').addEventListener('click', () => {
  // Ensure the audio context is in a `running` state
  if (audio.context && audio.context.state === 'suspended') {
    audio.context.resume().then(toggleMusic).catch(console.error);
  } else {
    toggleMusic();
  }
});

// Event listener for when the current song ends
audio.addEventListener('ended', () => {
  currentSongIndex++;
  if (currentSongIndex < playlist.length) {
    audio.src = playlist[currentSongIndex]; // Set the next song in the playlist
    audio.play().catch(error => {
      console.warn('Playback failed:', error);
      alert('Audio playback failed for the next track. Please try again.');
    });
  } else {
    // Restart the playlist from the beginning
    currentSongIndex = 0;
    audio.src = playlist[currentSongIndex];
    audio.play().catch(error => {
      console.warn('Playback failed:', error);
    });
  }
});

// Try to autoplay the audio on page load if the user has interacted before
if (userInteracted) {
  document.addEventListener('click', () => {
    audio.play().catch(error => {
      console.warn('Autoplay blocked or failed:', error);
      alert('Autoplay is blocked by your browser. Please press play to start the music.');
    });
  }, { once: true }); // Ensures this event listener only runs once after interaction
}
