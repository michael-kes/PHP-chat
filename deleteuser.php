<?php
  include "includes/db.php";
  include "includes/functions.php";

  session_start();

  $userToDelete = $_GET['delete'];
  $groupToDeleteIn = $_GET['groep'];

  // Om het simpel te houden gaan we natuurlijk geen toegang geven om jezelf te verwijderen
  if($userToDelete == $_SESSION['user_id'])
  {
    //TODO: Even aanpassen...
    echo "DIT BEN JE ZELF STOMME MONGOOL; je wordt automatisch doorverwezen naar de vorige pagina.";
    // Gebruiker tijd geven te lezen, vervolgens doorlinken.
    header('refresh:5;url=groep.php?groep=' . $groupToDeleteIn . '');
  }
  else{
    // Functie om gebruiker uit de groep te verwijderen
    deleteUserFromGroup($userToDelete, $groupToDeleteIn);
    // Redirect naar vorige groep
    header('Location: groep.php?groep=' . $groupToDeleteIn . '');
  }
?>
