<footer class="page-footer teal">
  <div class="container">
    <div class="row">
      <div class="col s6">
        <p class="grey-text text-lighten-4"><b>Ingelogd als: &nbsp; &nbsp;</b><a href="profile.php" class="white-text text-darken-2 underline tooltipped" data-position="top" data-delay="50" data-tooltip="Ga naar profiel"><?php echo $_SESSION['firstname'], "&nbsp;".$_SESSION['lastname']; ?></a></p>
      </div>
      <div class="col s6">
        <ul class="right">
          <li><a href="logout.php" class="grey-text text-lighten-4 underline">Uitloggen</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="footer-copyright">
    <div class="container">
      © 2017 Copyright Michael
    </div>
  </div>
</footer>

</body>

</html>