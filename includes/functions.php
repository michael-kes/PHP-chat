<?php

function escapeString($string)
{
    global $connection;
    return mysqli_real_escape_string($connection, trim($string));
}

function confirmQuery($result)
{
    global $connection;

    if (!$result)
    {
        die("QUERY FAILED " . mysqli_error($connection));
    }
}

function getAdmin($user_id)
{
    global $connection;

    $query = "SELECT * FROM  sw_user_group WHERE user_id = '$user_id' AND user_group_rights = 1";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(mysqli_num_rows($result) <= 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function getUserPermission($user_id, $group_id)
{
    global $connection;

    $rowPresent = false;

    $query = "SELECT user_group_rights
    FROM  sw_user_group
    WHERE user_id = '$user_id'
    AND group_id = '$group_id'";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(mysqli_num_rows($result) <= 0)
    {
        $rowPresent = false;
        return false;
    }
    else
    {
        while($row = mysqli_fetch_assoc($result))
        {
            return $row['user_group_rights'];
        }
    }

    if($rowPresent == true)
    {
      $row = mysqli_fetch_assoc($result);
      return $row;
  }
  else
  {
      $rowPresent = false;
      return false;
  }
}

function getSingleChannel($channel_id)
{
    global $connection;

    $query = "SELECT * FROM  sw_channel WHERE sw_channel.channel_id = '$channel_id'";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    $row = mysqli_num_rows($result);

    return $row;
}

function getChannelGroups()
{
    global $connection;

    //WHERE sw_group.channel_id = '" . $group_id . "'

    $query = "SELECT * FROM sw_group";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(mysqli_num_rows($result) <= 0)
    {
        return false;
    }

    while($row = mysqli_fetch_assoc($result))
    {
        $text_array[] = $row;
    }

    return $text_array;
}

/*function getAllSubscribersFromChannel($channel_id)
{
    global $connection;

    $text_array = array();
    $query = "SELECT sw_user.user_name, sw_user.user_firstname, sw_user.user_lastname, sw_user_group.user_id
    FROM sw_user_group
    INNER JOIN sw_user
    ON sw_user_group.user_id = sw_user.user_id
    INNER JOIN sw_group
    ON sw_user_group.group_id = sw_group.group_id
    WHERE sw_group.channel_id = '$channel_id'
    GROUP BY sw_user_group.user_id";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    while($rows = mysqli_fetch_assoc($result))
    {
        $text_array[] = $rows;
    }

    if(empty($text_array))
    {
        $text_array = false;
    }

    return $text_array;
}*/

function getAllSubscribersFromGroup($group_id)
{
    global $connection;

    $sub_array = array();
    $query =   "SELECT *
    FROM sw_user_group, sw_user
    WHERE sw_user_group.user_id = sw_user.user_id
    AND sw_user_group.group_id = '$group_id'";
    $result = mysqli_query($connection, $query);
    while($row = mysqli_fetch_assoc($result)) {
       $sub_array[] = $row;
   }
   if(empty($sub_array)) {
       $sub_array = false;
   }
   return $sub_array;
}

function getUserNames($user_id)
{
  global $connection;

  $query = "SELECT *
  FROM sw_user, sw_user_group
  WHERE sw_user.user_id = sw_user_group.user_id
  AND sw_user_group.group_id = '" . $user_id . "'";

  $result = mysqli_query($connection, $query);
  if(mysqli_num_rows($result) == 0)
  {
      return false;
  }

  while($row = mysqli_fetch_assoc($result))
  {
      $text_array[] = $row;
  }

  return $text_array;
}

function getSingleGroup($group_id)
{
    global $connection;

    $query = "SELECT *
    FROM  sw_group
    WHERE group_id = '" . $group_id . "'";
    $result = mysqli_query($connection, $query);

    if(mysqli_num_rows($result) <= 0) {
       return false;
   }
   $row = mysqli_fetch_assoc($result);

   return $row;
}

function subscribedGroup($user_id, $group_id)
{
    global $connection;

    $query = "SELECT sw_user_group.group_id, sw_user_group.user_id
    FROM sw_user_group
    INNER JOIN sw_group
    ON sw_group.group_id = sw_user_group.group_id
    WHERE sw_user_group.user_id = '$user_id'
    AND sw_group.group_id = '$group_id'";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(mysqli_num_rows($result) <= 0)
    {
        return false;
    }
    else
    {
        return true;
    }
}

function createGroup($group_name, $group_description, $group_password, $user_name)
{
    global $connection;

    $date = date('Y-m-d h:i:s', time());

    if(empty($group_password))
    {
        $query = "INSERT INTO sw_group (group_name, group_description, created_by) VALUES ('$group_name', '$group_description', '$user_name')";
    }
    else
    {
        $query = "INSERT INTO sw_group (group_name, group_description, group_password, created_by) VALUES ('$group_name', '$group_description', '$group_password', '$user_name')";
    }
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}

function unsubscribeGroup($user_id, $group_id)
{
    global $connection;

    $query = "DELETE t1
    FROM sw_user_group t1
    LEFT JOIN sw_group t2 ON t2.group_id = t1.group_id
    WHERE t1.user_id = '$user_id'
    AND t2.group_id = '$group_id'";

    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}

function getPublicGroup($group_id)
{
    global $connection;

    $query = "SELECT sw_group.group_id
    FROM sw_group
    WHERE sw_group.group_id = '$group_id'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_num_rows($result);

    confirmQuery($result);

    return $row;
}

/**
 * Subscribe to a private group
 * @param group_id, user_id, db_link
 */
function signupPrivateGroup($group_id, $user_id, $user_group_rights)
{
    global $connection;

    $errors = "";
    $checkSubscription = checkSubscription($group_id, $user_id);

    // Username validation, must contain between 4 and 40 characters.
    // Also checks if username already exists.
    if(!empty($checkSubscription))
    {
        foreach($checkSubscription as $key => $value)
        {
            if(($value['group_id']) && ($value['user_id']))
            {
                $errors .= "Je bent al geabonneerd op deze groep. <br />";
            }
        }
    }

    if(empty($errors))
    {
        addPrivateSubscriptionGroup($group_id, $user_id, $user_group_rights);
        $errors = "";
    }
    else
    {
        return $errors;
    }
}

/**
 * Check if duplications exist or going to exist
 * @param user_id, db_link
 */
function checkSubscription($group_id, $user_id)
{
    global $connection;

    $query = "SELECT * FROM sw_user_group
    WHERE sw_user_group.group_id = '$group_id'
    AND sw_user_group.user_id = '$user_id'";
    $result = mysqli_query($connection, $query);
    $row = mysqli_num_rows($result);

    confirmQuery($result);

    return $row;
}

/**
 * Function to add a private group
 */
function addPrivateSubscriptionGroup($group_id, $user_id, $user_group_rights)
{
    global $connection;

    $query = "INSERT INTO sw_user_group(group_id, user_id, user_group_rights)
    VALUES ('$group_id', '$user_id', '$user_group_rights')";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}

/**
  * Function to get the password of private group
  */
function getPrivateGroup($group_id)
{
    global $connection;

    $query = "SELECT sw_group.group_password, sw_group.group_id
    FROM  sw_group
    WHERE sw_group.group_id = '$group_id'
    AND sw_group.group_password NOT LIKE ''";
    $result = mysqli_query($connection, $query);
    $row = mysqli_fetch_assoc($result);

    confirmQuery($result);

    return $row;
}

/**
 * Query to delete a group
 */
function deleteGroup($group_id)
{
    global $connection;

    $query = "DELETE
    FROM sw_group
    WHERE sw_group.group_id = '$group_id'";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}

function createUser($user_name, $user_firstname, $user_lastname, $user_email, $user_password)
{
    //$date = date('Y-m-d h:i:s', time());

    global $connection;

    $query = "INSERT INTO sw_user(user_name, user_firstname, user_lastname, user_email, user_password)";
    $query .= "VALUES('$user_name', '$user_firstname', '$user_lastname', '$user_email', '$user_password')";

    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}

function getAllChats($group_id)
{
    global $connection;

    $query = "SELECT *
    FROM  sw_chat, sw_group, sw_user
    WHERE sw_chat.group_id = sw_group.group_id
    AND sw_chat.user_id = sw_user.user_id
    AND sw_chat.group_id = '$group_id'
    ORDER BY timemessage";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(escapeString(mysqli_num_rows($result) == 0))
    {
        return false;
    }
    else
    {
        while($row = mysqli_fetch_assoc($result))
        {
            $text_array[] = $row;
        }
    }
    return $text_array;
}


function getAllDmChats($user_two)
{
    global $connection;

    $query = "SELECT *
    , sw_user.user_name
    FROM sw_single_chat
    INNER JOIN sw_user ON sw_user.user_id = sw_single_chat.user_one_id
    WHERE sw_single_chat.user_two_id = $user_two";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(escapeString(mysqli_num_rows($result) == 0))
    {
        return false;
    }
    else
    {
        while($row = mysqli_fetch_assoc($result))
        {   
            $text_array[] = $row;
        }
    }
    return $text_array;
}


function addDmMessage($chat_id, $message, $user_one_id, $user_two_id, $db_link)
{
    global $connection;

    $query = "INSERT INTO `sw_single_chat` (`chat_id`, `chat_message`, `user_one_id`, `user_two_id`)
    VALUES ('$chat_id', '$message', $user_one_id, $user_two_id);";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}



function addMessage($chat_id, $chat_message, $user_id, $group_id, $db_link)
{
    global $connection;

    $query = "INSERT INTO sw_chat(chat_id,chat_message, user_id, group_id)";
    $query .= "VALUES ('$chat_id', '$chat_message', '$user_id', '$group_id')";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}



function editGroup($group_id, $group_name, $group_description, $group_password)
{
    global $connection;

    if (empty($group_password))
    {
        $query = "UPDATE sw_group
        SET group_name = '$group_name', group_description = '$group_description'
        WHERE group_id = '$group_id'";
    }
    else
    {
        $query = "UPDATE sw_group
        SET group_name = '$group_name', group_description = '$group_description', group_password = '$group_password'
        WHERE group_id = '$group_id'";
    }
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}

function editGroupPassword($group_id)
{
    global $connection;

    $query = "UPDATE sw_group
    SET group_password = ''
    WHERE group_id = '$group_id'";

    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    return $result;
}

function deleteGroupPermission($group_id, $user_id)
{
    global $connection;

    $query = "SELECT group_id, user_id
    FROM sw_user_group
    WHERE group_id = '$group_id'
    AND user_id = '$user_id'
    AND user_group_rights = 1";

    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(escapeString(mysqli_num_rows($result)) <= 0)
    {
        return false;
    }
    else
    {
        return true;
        /*while($row = mysqli_fetch_assoc($result))
        {
            return $row['created_by'];
        }*/
    }
}

function showAdmin($group_id)
{
    global $connection;

    $query = "SELECT created_by
    FROM sw_group
    WHERE group_id = '$group_id'";

    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(escapeString(mysqli_num_rows($result)) <= 0)
    {
        return false;
    }
    else
    {
        while($row = mysqli_fetch_assoc($result))
        {
            return $row['created_by'];
        }
    }
}

function usernameExists($username)
{
    global $connection;

    $query = "SELECT user_name FROM sw_user WHERE user_name = '$username'";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(escapeString(mysqli_num_rows($result)) <= 0)
    {
        return false;
    }
    else
    {
        return true;
        /*while($row = mysqli_fetch_assoc($result))
        {
            return $row['username'];
        }*/
    }

}

function emailExists($email)
{
    global $connection;

    $query = "SELECT user_email FROM sw_user WHERE user_email = '$email'";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(escapeString(mysqli_num_rows($result)) <= 0)
    {
        return false;
    }
    else
    {
        return true;
        /*while($row = mysqli_fetch_assoc($result))
        {
            return $row['username'];
        }*/
    }

}

function groupnameExists($groupname)
{
    global $connection;

    $query = "SELECT group_name FROM sw_group WHERE group_name = '$groupname'";
    $result = mysqli_query($connection, $query);

    confirmQuery($result);

    if(escapeString(mysqli_num_rows($result)) <= 0)
    {
        return false;
    }
    else
    {
        return true;
        /*while($row = mysqli_fetch_assoc($result))
        {
            return $row['username'];
        }*/
    }

}

function generateHash($password)
{
    // These only work for CRYPT_SHA512, but it should give you an idea of how crypt() works.
    $Salt = uniqid(); // Could use the second parameter to give it more entropy.
    $Algo = '6'; // This is CRYPT_SHA512 as shown on http://php.net/crypt
    $Rounds = '7000'; // The more, the more secure it is!

    // This is the "salt" string we give to crypt().
    $CryptSalt = '$' . $Algo . '$rounds=' . $Rounds . '$' . $Salt;

    $HashedPassword = crypt($password, $CryptSalt);

    return $HashedPassword;
}

function verifyPassword($password, $hashedPassword)
{
    // Now, what about checking if a password is the right password?
    if (crypt($password, $hashedPassword) == $hashedPassword)
    {
        return true;
    }
    else
    {
        return false;
    }
}

function deleteUserFromGroup($user_id, $group_id)
{
  global $connection;

  $query = "DELETE FROM sw_user_group
  WHERE sw_user_group.user_id = '$user_id'
  AND sw_user_group.group_id = '$group_id'";
  $result = mysqli_query($connection, $query);

  confirmQuery($result);

  return $result;
}

function setSession($user_id, $username, $firstname, $lastname, $email)
{
    $_SESSION['user_id']    = $user_id;
    $_SESSION['username']   = $username;
    $_SESSION['firstname']  = $firstname;
    $_SESSION['lastname']   = $lastname;
    //$_SESSION['role']       = $role;
    $_SESSION['email']      = $email;
}

function deleteSession()
{
    $_SESSION['user_id']    = null;
    $_SESSION['username']   = null;
    $_SESSION['firstname']  = null;
    $_SESSION['lastname']   = null;
    //$_SESSION['role']       = null;
    $_SESSION['email']      = null;
}

?>
