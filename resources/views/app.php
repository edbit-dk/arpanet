<?php include 'template/header.php'; ?>
  <div id="terminal-wrapper">
  <button style="background-color: #00935E;" id="play-button">MUSIC</button>
  <button style="background-color: #00935E;" onclick="location.href='teleterm.txt'" target="_blank" type="button">HELP</button>
    <div id="terminal"></div>
  </div>

  <div id="prompt">
    <?php if(user()->auth()): ?>
      <span id="user"><?php echo strtoupper(auth()->user_name) ?>@<?php echo host()->hostname() ?>></span> 
      <?php else: ?> 
      <span id="user">></span> 
    <?php endif; ?>
      <input type="text" id="command-input" autofocus spellcheck="false" autocomplete="off">
    </div>
    
    <script>
       // Playlist of songs
      const playlist = [
        <?php foreach($_SESSION['music'] as $music): ?>
          <?php echo "'$music',"; ?>
        <?php endforeach ?>
      ];
    </script>
  </div>
<?php include 'template/footer.php'; ?>