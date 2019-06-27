<?php
/* BEGIN CHANNEL */
/*
  @param Get single channel from the database
  @param
  @param
*/
function createChannel($channel_name, $channel_description, $channel_password, $db_link)
{
  if(empty($channel_password))
  {
    $query = "INSERT INTO sw_channel (channel_name, channel_description, channel_password) VALUES ('" . $channel_name . "', '" .  $channel_description . "', '" .  $channel_password . "')";
  }
  else
  {
    $query = "INSERT INTO sw_channel (channel_name, channel_description, channel_password) VALUES ('" . $channel_name . "', '" .  $channel_description . "', '" .  generateHash($channel_password) . "')";
  }
  $result = databaseQuery($query, $db_link);
  return $result;
}
// Creates group and checks for password field, if empty make group public.
function createGroup($group_name, $group_description, $group_password, $channel_id, $uid, $db_link)
{
  $date = date('Y-m-d h:i:s', time());
  if(empty($group_password))
  {
    $query = "INSERT INTO sw_group (group_name, group_description, group_password, channel_id, created_at) VALUES ('" . $group_name . "', '" .  $group_description . "', '" . $group_password . "', '" . $channel_id . "', '" . $date . "')";
  }
  else
  {
    $query = "INSERT INTO sw_group (group_name, group_description, group_password, channel_id, created_at) VALUES ('$group_name', '$group_description', '" . generateHash($group_password) . "', '$channel_id', '$date')";
  }
  $result = databaseQuery($query, $db_link);
  return $result;
}
// Sets rights when creating a channel.
function setGroupRightsAdmin($uid, $db_link)
{
  // PARAM LAST_INSERT_ID() = Last created incremented ID (group_id).
  $query = "INSERT INTO sw_user_group (group_id, user_id, user_group_rights) VALUES (LAST_INSERT_ID(), '" . $uid . "', '1')";

  $result = databaseQuery($query, $db_link);

  return $result;
}

function checkChannelname($channel_name, $db_link)
{
  $query = "SELECT sw_channel.channel_name FROM sw_channel WHERE sw_channel.channel_name = '" . $channel_name . "'";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}

function checkChannelRegister($channel_name, $channel_description, $channel_password, $channel_password_check, $db_link)
{
  $errors = "";
  $checkChannelname = checkChannelname($channel_name, $db_link);
  // Checks if channelname already exists.
  if(!empty($checkChannelname))
  {
    foreach($checkChannelname as $value)
    {
      if($value === $channel_name)
      {
        $errors .= "Kanaalnaam bestaat al. <br />";
      }
    }
  }

  if(empty($errors))
  {
    createChannel($channel_name, $channel_description, $channel_password, $db_link);
    return true;
  }
  else
  {
    return $errors;
  }
}

/**
 * Function to get the newewst channel id
 */
function getNewestChannelID($channel_id, $db_link)
{
  $query = "SELECT channel_id
            FROM sw_channel
            ORDER BY channel_id DESC
            LIMIT 1";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}

/**
 * Get channel group id
 */
function getChannelGroupID($group_id, $db_link)
{
  $query = "SELECT group_id
            FROM sw_group
            ORDER BY group_id DESC
            LIMIT 1";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}

/**
 * Get the channel group id
 */
function getChannelUnsub($channel_id, $db_link)
{
  $query = "SELECT channel_group_id
            FROM sw_channel
            WHERE channel_id = '$channel_id'";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}

/**
 * Function to set the group id in the channel table
 */
function setChannelGroupIDinChannel($channel_group_id, $channel_id, $db_link)
{
  $query = "UPDATE sw_channel SET channel_group_id = '$channel_group_id' WHERE channel_id = '$channel_id'";
  $result = databaseQuery($query, $db_link);
  return $result;
}

/**
 * Function to get just one channel
 */
function getSingleChannel($channel_id) {
  global $connection;

  $query = "SELECT *
      FROM  sw_channel
      WHERE sw_channel.channel_id = '$channel_id'";
  $result = mysqli_query($connection, $query);
  $row = mysqli_num_rows($result);
  return $row;
}

/**
 * Get all channel from the database
 */
function getAllChannels($channel_id, $db_link) {
  $query = "SELECT *
      FROM  sw_channel
      ORDER BY channel_id";
  $result = databaseQuery($query, $db_link);
  while($row = databaseFetchRow($result)) {
    $text_array[] = $row;
  }
  return $text_array;
}
/*
  @param Get all channel from the database
  @param
  @param
*/
function getAllSubscribersFromChannel($channel_id,  $db_link) {
	$text_array = array();
  $query = "SELECT sw_user.user_name, sw_user.user_firstname, sw_user.user_lastname, sw_user_group.user_id
			FROM sw_user_group
			INNER JOIN sw_user
			ON sw_user_group.user_id = sw_user.user_id
			INNER JOIN sw_group
			ON sw_user_group.group_id = sw_group.group_id
			WHERE sw_group.channel_id = '" . $channel_id . "'
			GROUP BY sw_user_group.user_id";
  $result = databaseQuery($query, $db_link);
  while($rows = databaseFetchRow($result)) {
    $text_array[] = $rows;
  }
  if(empty($text_array)) {
	  $text_array = false;
  }
  return $text_array;
}

/*  ===================
	FUNCTIONS FOR GROUPS
	=================== */
/**
  * Function to get the password of private group
  */
function getPrivateGroup($group_id, $db_link)
{
  $query = "SELECT sw_group.group_password, sw_group.group_id
            FROM  sw_group
            WHERE sw_group.group_id = '$group_id'
            AND sw_group.group_password NOT LIKE ''";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}
/**
  * Same function as getPrivateGroup but we dont want the password
  * for the public group
  */
function getPublicGroup($group_id, $db_link)
{
  $query = "SELECT sw_group.group_id
            FROM sw_group
            WHERE sw_group.group_id = '$group_id'";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}
/**
 * Return easy subscribed func for groups
 */
function subscribedGroup($user_id, $group_id, $db_link)
{
  $query = "SELECT sw_user_group.group_id, sw_user_group.user_id
            FROM sw_user_group
            INNER JOIN sw_group
            ON sw_group.group_id = sw_user_group.group_id
            WHERE sw_user_group.user_id = '$user_id'
            AND sw_group.group_id = '$group_id'";
  $result = databaseQuery($query, $db_link);
  $rows = databaseNumRows($result);
  if($rows <= 0)
  {
    return false;
  }
  else
  {
    return true;
  }
}
/**
 * Subscribe to a private group
 * @param group_id, user_id, user_group_rights, db_link
 */
function signupPrivateGroup($group_id, $user_id, $user_group_rights, $db_link) {
  $errors = "";
  $checkSubscription = checkSubscription($group_id, $user_id, $db_link);
  // Username validation, must contain between 4 and 40 characters.
  // Also checks if username already exists.
  if(!empty($checkSubscription))
  {
    foreach($checkSubscription as $key => $value)
    {
      if(($value['group_id']) && ($value['user_id']))
      {
        $errors .= "Je bent al geabonneerd op dit groep. <br />";
      }
    }
  }
  if(empty($errors))
  {
    addPrivateSubscriptionGroup($group_id, $user_id, $user_group_rights, $db_link);
    $errors = "";
  }
  else
  {
    return $errors;
  }
}

/**
 * Function to add a private group
 */
function addPrivateSubscriptionGroup($group_id, $user_id, $user_group_rights, $db_link)
{
    $query = "INSERT INTO sw_user_group(group_id, user_id, user_group_rights)
              VALUES ($group_id, $user_id, $user_group_rights)";
    $result = databaseQuery($query, $db_link);
    return $result;
}

/**
 * Query to unsub from a group
 */
function unsubscribeGroup($user_id, $group_id, $db_link)
{
  $query = "DELETE t1
            FROM sw_user_group t1
            LEFT JOIN sw_group t2 ON t2.group_id = t1.group_id
            WHERE t1.user_id = '$user_id'
            AND t2.group_id = '$group_id'";

  $result = databaseQuery($query, $db_link);
  return $result;
}

/**
 * Query to delete a group
 */
function deleteGroup($group_id, $db_link) {
    $query = "DELETE
              FROM sw_group
              WHERE sw_group.group_id = '$group_id'";
    $result = databaseQuery($query, $db_link);
    return $result;
  }

/* ===================
END FUNCTIONS FOR GROUPS
=================== */

/**
 * Function to get the password of private channel
 */
function getPrivateChannel($channel_id, $db_link) {
  $query = "SELECT sw_channel.channel_password, sw_channel.channel_group_id
      FROM  sw_channel
      WHERE sw_channel.channel_id = '$channel_id'
      AND sw_channel.channel_password NOT LIKE ''";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}

/**
 * Same function as getPrivateChannel but we dont want the password
 * for the public channel
 */
function getPublicChannel($channel_id, $db_link) {
  $query = "SELECT sw_channel.channel_group_id
            FROM sw_channel
            WHERE sw_channel.channel_id = '$channel_id'";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}

/**
 * Check if duplications exist or going to exist
 * @param user_id, db_link
 */
function checkSubscription($group_id, $user_id, $db_link) {
  $query = "SELECT * FROM sw_user_group
            WHERE sw_user_group.group_id = '$group_id'
            AND sw_user_group.user_id = '$user_id'";
  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  return $row;
}

/**
 * Return easy subscribed func
 */
function subscribed($user_id, $channel_group_id, $db_link) {
  $query = "SELECT group_id, user_id, channel_group_id
            FROM sw_user_group
            INNER JOIN sw_channel
              ON sw_channel.channel_group_id = sw_user_group.group_id
            WHERE sw_user_group.user_id = '$user_id'
            AND sw_channel.channel_group_id = '$channel_group_id'";
  $result = databaseQuery($query, $db_link);
  $rows = databaseNumRows($result);
  if($rows <= 0) {
    return false;
  }
  else {
    return true;
  }
}

/**
 * Subscribe to a private channel
 * @param channel_id, group_id, user_id, user_group_rights, db_link
 */
function signupPrivateChannel($channel_id, $group_id, $user_id, $user_group_rights, $db_link) {
  $errors = "";
  $checkSubscription = checkSubscription($group_id, $user_id, $db_link);
  // Username validation, must contain between 4 and 40 characters.
  // Also checks if username already exists.
  if(!empty($checkSubscription))
  {
    foreach($checkSubscription as $key => $value)
    {
      if(($value['group_id']) && ($value['user_id']))
      {
        $errors .= "Je bent al geabonneerd op dit kanaal. <br />";
      }
    }
  }
  if(empty($errors)) {
    addPrivateSubscription($channel_id, $group_id, $user_id, $user_group_rights, $db_link);
    $errors = "";
  }
  else {
    return $errors;
  }
}

/**
 * Add a subscription
 */
function addPrivateSubscription($channel_id, $group_id, $user_id, $user_group_rights, $db_link) {
    $query = "INSERT INTO sw_user_group(group_id, user_id, user_group_rights)
              VALUES ($group_id, $user_id, $user_group_rights)";
    $result = databaseQuery($query, $db_link);
    return $result;
}
/**
 * Check if someone owns the channel he is in
 * AND sw_user_group.user_group_rights = '1'
 */
//WHERE sw_user_group.user_group_rights = '1'
function getSingleUserGroupRightChannel($user_id, $channel_group_id, $db_link) {
  $query = "SELECT group_id, user_id, channel_group_id, user_group_rights
            FROM sw_user_group
            INNER JOIN sw_channel
              ON sw_channel.channel_group_id = sw_user_group.group_id
            WHERE sw_user_group.user_id = '$user_id'
            AND sw_channel.channel_group_id = '$channel_group_id'
            ";

  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);

  return $row;
  /* if($row <= 0)
  {
    return false;
  }
  else
  {
    return true;
  } */
}

function getSingleUserGroupRightGroup($user_id, $group_id, $db_link) {
  $query = "SELECT sw_user_group.group_id, user_id, user_group_rights
            FROM sw_user_group
            INNER JOIN sw_group
              ON sw_group.group_id = sw_user_group.group_id
            WHERE sw_user_group.user_id = '$user_id'
            AND sw_group.group_id = '$group_id'
            AND sw_user_group.user_group_rights = '1'
            OR sw_user_group.user_group_rights = '2'";

  $result = databaseQuery($query, $db_link);
  $row = databaseFetchRow($result);
  if($row <= 0)
  {
    return false;
  }
  else
  {
    return true;
  }
}

/**
 * Query to unsub from a channel
 */
function unsubscribe($user_id, $channel_id, $channel_group_id, $db_link) {
  $query = "DELETE t1
            FROM sw_user_group t1
            LEFT JOIN sw_channel t2 ON t2.channel_group_id = t1.group_id
            WHERE t1.user_id = '$user_id'
            AND t2.channel_id = '$channel_id'
            AND t2.channel_group_id = '$channel_group_id'";

  $result = databaseQuery($query, $db_link);
  return $result;
}

/**
 * Function to get the admin
 */
function getAdmin($user_id, $db_link)
{
  $query = "SELECT *
            FROM  sw_user_group
            WHERE user_id = '$user_id'
            AND user_group_rights = 1";
  $result = databaseQuery($query, $db_link);
  $rows = databaseNumRows($result);
  if($rows <= 0)
  {
    return false;
  }
  else
  {
    return true;
  }
}

/**
 * Function to delete channel including all its childs
 */
function deleteChannel($channel_id, $channel_group_id, $db_link) {
  $query = "DELETE t1, t2, t3
            FROM sw_user_group t1
            LEFT JOIN sw_channel t2 ON t2.channel_group_id = t1.group_id
            LEFT JOIN sw_group t3 on t3.channel_id = t2.channel_id
            WHERE t2.channel_id = '$channel_id'
            AND t2.channel_group_id = '$channel_group_id'";

    $result = databaseQuery($query, $db_link);
    return $result;
}

function editChannel($channel_id, $channel_name, $channel_description, $channel_password, $db_link)
{
  if (empty($channel_password))
  {
    $query = "UPDATE sw_channel
              SET channel_name = '$channel_name', channel_description = '$channel_description'
              WHERE channel_id = '$channel_id'";
  }
  else
  {
    $query = "UPDATE sw_channel
              SET channel_name = '$channel_name', channel_description = '$channel_description', channel_password = '" . generateHash($channel_password) . "'
              WHERE channel_id = '$channel_id'";
  }
  $result = databaseQuery($query, $db_link);
  return $result;
}

function editChannelPassword($channel_id, $db_link)
{
    $query = "UPDATE sw_channel
              SET channel_password = ''
              WHERE channel_id = '$channel_id'";
  $result = databaseQuery($query, $db_link);
  return $result;
}
function editGroup($group_id, $group_name, $group_description, $group_password, $db_link)
{
  if (empty($group_password))
  {
    $query = "UPDATE sw_group
              SET group_name = '$group_name', group_description = '$group_description'
              WHERE group_id = '$group_id'";
  }
  else
  {
    $query = "UPDATE sw_group
              SET group_name = '$group_name', group_description = '$group_description', group_password = '" . generateHash($group_password) . "'
              WHERE group_id = '$group_id'";
  }
  $result = databaseQuery($query, $db_link);
  return $result;
}

function editGroupPassword($group_id, $db_link)
{
    $query = "UPDATE sw_group
              SET group_password = ''
              WHERE group_id = '$group_id'";

  $result = databaseQuery($query, $db_link);
  return $result;
}
/* END CHANNEL */

/* ETC FUNCTIONS */

/**
 * Used to sort the dashboard on name
 */
function compare_channel_names($a, $b) {
	return strnatcmp($a['channel_name'], $b['channel_name']);
}

function compare_group_names($a, $b) {
  return strnatcmp($a['group_name'], $b['group_name']);
}

/* END ETC FUNCTIONS */
?>
