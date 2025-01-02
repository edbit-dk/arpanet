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

// Function to play the next song
function playNextSong() {
  if (playlist.length === 0) {
    console.log('Playlist is empty.');
    return;
  }

  currentSongIndex = (currentSongIndex + 1) % playlist.length; // Move to the next song, wrap around if needed
  audio.src = playlist[currentSongIndex];
  audio.play()
    .then(() => {
      console.log(`Playing next song: ${playlist[currentSongIndex]}`);
      document.getElementById('play-button').textContent = 'MUSIC STOP';
    })
    .catch(error => {
      console.error('Playback failed:', error);
      alert('Audio playback failed. Please try again or interact with the page.');
    });
}

// Function to toggle play/pause for music
function toggleMusic() {
  initializeAudio();

  if (audio.paused) {
    audio.play().then(() => {
      console.log('Audio started playing.');
      document.getElementById('play-button').textContent = 'MUSIC STOP'; // Update button text
    }).catch(error => {
      console.error('Playback failed:', error);
      alert('Audio playback failed. Please try again or interact with the page.');
    });
  } else {
    audio.pause();
    console.log('Audio paused.');
    document.getElementById('play-button').textContent = 'MUSIC PLAY'; // Update button text
  }
}

// Function to handle when the current song ends
function handleAudioEnded() {
  playNextSong(); // Automatically play the next song when the current one ends
}

// Event listener for the play button
document.getElementById('play-button').addEventListener('click', toggleMusic);
