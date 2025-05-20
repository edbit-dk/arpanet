<?php include 'template/header.php'; ?>
  <div id="terminal-wrapper">
  <button style="display: none;" id="play-button">MUSIC</button>
  <button style="display: none;" onclick="location.href='teleterm.txt'" target="_blank" type="button">HELP</button>
    <div id="terminal"></div>
    <div id="prompt">
    <span id="connection"></span><input type="text" id="command-input" autofocus spellcheck="false" autocomplete="off">
  </div>
  </div>
    
    <script>
       // Playlist of songs
      const playlist = [
        <?php foreach(session()->get('music') as $music): ?>
          <?php echo "'$music',"; ?>
        <?php endforeach ?>
      ];
    </script>
  </div>
<?php include 'template/footer.php'; ?>