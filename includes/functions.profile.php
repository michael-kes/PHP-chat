<?php
/**
 * Functions file for everything used within a profile
 */

/**
 * Count how many users are avaiable for Direct Messaging
 */
function countTotalUsers($user_id, $connection) {

 global $connection;

 $amount = '';
 $query = "SELECT COUNT(sw_user.user_id) AS amount
 FROM sw_user
 INNER JOIN sw_single_chat
 ON sw_user.user_id=sw_single_chat.user_two_id
 ORDER BY COUNT(sw_user.user_id);";

 $result = databaseQuery($query, $connection);
 while($row = databaseFetchRow($result)) {


  $amount = $row['amount'];
  return $amount;
}
}



function createChat($chat_id, $user_one_id, $user_two_id, $connection)
{
  $connection;

  $query = "INSERT INTO sw_single_chat(chat_id, user_one_id, user_two_id)";
  $query .= "VALUES ('$chat_id', '$user_one_id', '$user_two_id')";
  $result = mysqli_query($connection, $query);

  confirmQuery($result);

  return $result;
}


/**
 * /Users which are already in DM
 */
function showExistingUsernames($user_id, $connection) {

   global $connection;

   $query = "SELECT
      sw_user.user_id
   ,  sw_user.user_name
   FROM sw_user
   GROUP BY sw_user.user_name";

   $result = databaseQuery($query, $connection);
   while($row = databaseFetchRow($result)) {

    if($row['user_id'] == $_SESSION['user_id'])
    {
      echo "";
    }
    else
    {
      $totalNewMessages = countTotalNewMessages($user_id, $row['user_id'], $connection);
      echo "<div class='collapsible-body'>
              <div class='listItem'>";
      echo "<a href='singlechat.php?user_two=".$row['user_id']."' class='black-text'>".$row['user_name']."</a>";
      echo '<span class="secondary-content black-text"><i>U heeft ' . $totalNewMessages . ' nieuwe berichten!</i></span>';
      echo "  </div>
            </div>";
    }
  }
}

function countTotalNewMessages($user_id, $user_two_id, $connection) {
  global $connection;

  $query = "SELECT COUNT(sw_single_chat.visited)
            AS amount
            FROM sw_single_chat
            WHERE user_one_id = '$user_id'
            AND user_two_id = '$user_two_id'
            AND visited = 0";

  $result = databaseQuery($query, $connection);
  while($row = databaseFetchRow($result))
  {
    $amount = $row['amount'];
  }

  return $amount;
}








?>
