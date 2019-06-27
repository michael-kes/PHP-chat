<nav>
  <div class="nav-wrapper teal">
    <ul id="nav-mobile" class="left hide-on-med-and-down" style="margin-left: 15%;">
      <?php
      if(basename($_SERVER['PHP_SELF']) == 'groep.php')
      { ?>
          <li><a href="groepen.php" class="tooltipped" data-position="left" data-delay="50" data-tooltip="Ga naar groepen"><i class="material-icons">home</i></a></li>
          <li><a href="groepen.php">Home</a></li>
      <?php } ?>
      <?php
      if(basename($_SERVER['PHP_SELF']) == 'profile.php')
      { ?>
          <li><a href="groepen.php" class="tooltipped" data-position="left" data-delay="50" data-tooltip="Ga naar groepen"><i class="material-icons">home</i></a></li>
          <li><a href="groepen.php">Home</a></li>
      <?php } ?>
      <?php
      if(basename($_SERVER['PHP_SELF']) == 'singlechat.php')
      { ?>
          <li><a href="groepen.php" class="tooltipped" data-position="left" data-delay="50" data-tooltip="Ga naar groepen"><i class="material-icons">home</i></a></li>
          <li><a href="groepen.php">Home</a></li>
      <?php } ?>
    </ul>
  </div>
</nav>
