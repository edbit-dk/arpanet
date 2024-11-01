<?php include 'template/header.php'; ?>

  <div id="terminal-wrapper">
    <div id="terminal"></div>
  </div>
  <div id="prompt">
    <?php if(user()->auth()): ?>
      <span id="user"><?php echo strtoupper(auth()->user_name) ?>@<?php echo host()->hostname() ?>></span> 
      <?php else: ?> 
      <span id="user">.></span> 
    <?php endif; ?>
      <input type="text" id="command-input" autofocus spellcheck="false" autocomplete="off">
  </div>

<?php include 'template/footer.php'; ?>