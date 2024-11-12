<?php include 'template/header.php'; ?>

  <div id="terminal-wrapper">
    <div id="terminal"></div>
  </div>
  <div id="prompt">
    <?php if(user()->auth()): ?>
      <span id="user"><?php echo strtoupper(auth()->user_name) ?>@<?php echo host()->hostname() ?>></span> 
      <?php else: ?> 
      <span id="user">></span> 
    <?php endif; ?>
      <input type="text" id="command-input" autofocus spellcheck="false" autocomplete="off">
      <button style="background-color: #00FF00;" id="play-button">music</button>
    </div>

    <script>
       // Playlist of songs
      const playlist = [
        <?php foreach($_SESSION['music'] as $music): ?>
          <?php echo "'$music',"; ?>
        <?php endforeach ?>
      ];
    </script>

<?php include 'template/footer.php'; ?>