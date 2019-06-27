<?php
	/* BEGIN USERS */

	/*
		@param Get single user from the database
		@param
		@param
	*/
	function getUser($user_id = 0, $user_name = "", $db_link)
	{
		$query = "SELECT * FROM sw_user";
		if($user_id > 0 && $user_name != "")
		{
			$query .= " WHERE sw_user.user_id = '". $user_id . "' AND sw_user.user_name = '" . $user_name . "'";
		}
		else if($user_id > 0)
		{
			$query .= " WHERE sw_user.user_id = '". $user_id . "'";
		}
		else if ($user_name != "")
		{
			$query .= " WHERE sw_user.user_name = '". $user_name . "'";
		}
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0)
		{
			return false;
		}

		$row = databaseFetchRow($result);

		return $row;
	}

	function getUserName($user_id, $db_link)
	{
		$query = "SELECT user_firstname, user_lastname
							FROM sw_user
							WHERE user_id = $user_id";

		$result = databaseQuery($query, $db_link);
		$row = databaseFetchRow($result);

		return $row;
	}

	/*
		@param Get all users from the database
		@param
		@param
	*/
	function getAllUsers($user_id = 0, $db_link)
	{
		$query = "SELECT * FROM sw_user";
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0)
		{
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	function createUser($user_name, $user_firstname, $user_lastname, $user_email, $user_password, $db_link)
	{
		$date = date('Y-m-d h:i:s', time());

		$query = "INSERT INTO sw_user (user_name, user_firstname, user_lastname, user_email, user_password, user_lastlogin, user_created_at) VALUES ('" . $user_name . "', '" . $user_firstname . "', '" .  $user_lastname . "', '" . $user_email . "', '" .  generateHash($user_password) . "', '" . $date . "', '" . $date . "')";


		$result = databaseQuery($query, $db_link);

		return $result;
	}

	function addMessage($chat_message, $user_id, $group_id, $db_link) {
		$query = "INSERT INTO sw_chat(chat_message, user_id, group_id) VALUES ('" . $chat_message . "', '" . $user_id . "', '" . $group_id . "')";
		$result = databaseQuery($query, $db_link);

		return $result;
	}

	function addSingleMessage($chat_message, $user_one_id, $user_two_id, $db_link) {
		$query = "INSERT INTO sw_single_chat(chat_message, user_one_id, user_two_id) VALUES ('" . $chat_message . "', '" . $user_one_id . "', '" . $user_two_id . "')";

		$result = databaseQuery($query, $db_link);

		return $result;
	}

	function addUpload($password, $file_path, $file_name, $enc_name, $block_id, $db_link) {
		$query =    "INSERT INTO sw_upload (upload_password, upload_path, upload_filename, upload_encname, block_id)" .
					"VALUES ('" . $password . "', '" . $file_path . "', '" . $file_name . "', '" . $enc_name . "', '" . $block_id . "');";
		$result = databaseQuery($query, $db_link);

		return $result;
	}

	/* BEGIN LOGIN */
	function checkUserValidation($username, $passwd, $db_link)
	{
		$errors = "";

		$query = "SELECT user_validated FROM sw_user WHERE sw_user.user_name = '$username'";
		$result = databaseQuery($query, $db_link);
		$row = databaseFetchRow($result);

		if(empty($row['user_validated']))
		{
			return true;
		}
		else {
			$errors .= "Activeert u eerst uw account via de verzonden mail!";

			return $errors;
		}
	}

	function checkLogin($username, $passwd, $db_link)
	{
		$errors = "";

		$query = "SELECT * FROM sw_user";

		if($username != "" && $passwd != "")
		{
			$query .= " WHERE sw_user.user_name = '" . $username. "'";

			$result = databaseQuery($query, $db_link);

			if(databaseNumRows($result) <= 0)
			{
				$errors .= "Ongeldige combinatie ingevoerd! ";
			}
			else {
				$row = databaseFetchRow($result);

				if(verifyPassword($passwd, $row['user_password']) == true)
				{
					return true;
				}
				else
				{
					$errors .= "Ongeldige combinatie ingevoerd";
				}
			}
		}
		else
		{
			$errors .= "Vul een gebruikers naam en wachtwoord in! ";
		}

		return $errors;
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

	/* END LOGIN */
	/* END USERS */
	/* BEGIN CHANNEL */

	/* BEGIN GROUPS */

	/*
		@param Get all groups from the database
		@param
		@param
	*/
	function getUserPermission($user_id, $group_id, $db_link) {
		$query =   "SELECT *
					FROM  sw_user_group
					WHERE user_id = '" . $user_id . "'
					AND group_id = '" . $group_id . "'";
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}

		while($row = databaseFetchRow($result)){
			return $row['user_group_rights'];
		}
		return $false;
	}
	/*
		@param Get all groups from the database
		@param
		@param
	*/
	function getAllGroups($db_link) {
		$query = "SELECT *
				FROM  sw_group";
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0)
		{
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	function getChannelGroups($group_id, $db_link) {
		$query = "SELECT *
				FROM  sw_group
				WHERE sw_group.channel_id = '" . $group_id . "'";
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	/*
		@param Get single group from the database
		@param
		@param
	*/
	function getSingleGroup($group_id,  $db_link) {
		$query = "SELECT *
				FROM  sw_group
				WHERE group_id = '" . $group_id . "'";
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}
		$row = databaseFetchRow($result);

		return $row;
	}
	/*
		@param Get single upload row from the database
		@param
		@param
	*/
	function getSingleUpload($upload_id,  $db_link) {
		$query = "SELECT *
				FROM  sw_upload
				WHERE upload_id = '" . $upload_id . "'";
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}
		$row = databaseFetchRow($result);

		return $row;
	}

	/* END GROUPS */

	/* BEGIN BLOCK */

	/*
		@param Get single block from the database
		@param
		@param
	*/
	function getSingleBlock($block_id, $db_link) {
		$query = "SELECT *
				FROM  sw_block
				WHERE block_id = '" . $block_id . "'";

		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}
		$row = databaseFetchRow($result);
		return $row;
	}

	/*
		@param Get all blocks from the database
		@param
		@param
	*/
	function getBlocks($group_id, $db_link)
	{
		$block_array = array();
		$query =   "SELECT *
					FROM  sw_block
					WHERE group_id = '" . $group_id ."'";
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0){
			return false;
		}

		while($row = databaseFetchRow($result)){
			$block_array[] = $row;
		}

		return $block_array;
	}

	/* END BLOCK */

	/* BEGIN CHAT */

	/*
		@param Get single chat from the database
		@param
		@param
	*/
	function getSingleChat($chat_id, $db_link) {
		$query = "SELECT *
				FROM  sw_chat
				INNER JOIN sw_user
				ON sw_chat.user_id = sw_user.userID
				WHERE sw_chat.chat_id = '" . $chat_id . "'";
		$result = databaseQuery($query, $db_link);
		$row = databaseFetchRow($result);

		return $row;
	}

	/*
		@param Get all blocks from the database
		@param
		@param
	*/
	function getAllChats($block_id, $db_link)
	{
		$query = "SELECT *
				  FROM  sw_chat, sw_group, sw_user
				  WHERE sw_chat.group_id = sw_group.group_id
				  AND sw_chat.user_id = sw_user.user_id
                  AND sw_chat.group_id = '" . $block_id . "'";

		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	function getSingleChats($user_id, $user_two_id, $db_link)
	{
		$query = "SELECT *
							FROM `sw_single_chat`
							WHERE user_one_id = $user_id
							AND user_two_id = $user_two_id";

		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	function getAllSingleChats($user_id, $user_two_id, $db_link)
	{
		$query = "SELECT *
							FROM sw_single_chat, sw_user
							WHERE 1 = 1
							AND (sw_single_chat.user_one_id = $user_id OR sw_single_chat.user_one_id = $user_two_id)
							AND (sw_single_chat.user_two_id = $user_two_id OR sw_single_chat.user_two_id = $user_id)
							AND sw_single_chat.user_one_id = sw_user.user_id";

		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	/* END CHAT */

	/* BEGIN USER_GROUP */

	/*
		@param Get all user group by users from the database
		@param
		@param
	*/
	function getUserGroupByUsers($user_group,  $db_link) {
		$query = "SELECT *
				FROM  sw_user_group
				INNER JOIN sw_group
				ON sw_user_group.group_id = sw_group.group_id
				INNER JOIN sw_channel
				ON sw_user_group.channel_id = sw_channel.channel_id
				INNER JOIN sw_user
				ON sw_user_group.user_id = sw_user.user_id
				WHERE sw_user_group.user_id =" . $user_group;
		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0)
		{
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	/* END USER_GROUP */

	function getUserNames($user_id, $db_link)
	{
		$query = "SELECT *
		FROM sw_user, sw_user_group
		WHERE sw_user.user_id = sw_user_group.user_id
		AND sw_user_group.group_id = '" . $user_id . "'";

		$result = databaseQuery($query, $db_link);
		if(databaseNumRows($result) == 0)
		{
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$text_array[] = $row;
		}

		return $text_array;
	}

	function getAllSubscribersFromGroup($group_id,  $db_link) {
		$sub_array = array();
		$query =   "SELECT *
					FROM sw_user_group, sw_user
					WHERE sw_user_group.user_id = sw_user.user_id
					AND sw_user_group.group_id = '" . $group_id . "'";
		$result = databaseQuery($query, $db_link);
		while($row = databaseFetchRow($result)) {
			$sub_array[] = $row;
		}
		if(empty($sub_array)) {
			$sub_array = false;
		}
		return $sub_array;
	}
	/* BEGIN LOGIN */

	function setLoginSessions($user_id, $username) {
		$_SESSION["user_id"] = $user_id;
		$_SESSION["login_timestamp"] = time();
	}

	function setLastLogin($user_id, $db_link) {
		$_SESSION["user_id"] = $user_id;
		$date = date('Y-m-d h:i:s', time());
		$query = "UPDATE sw_user
							SET user_lastlogin = '$date'
							WHERE user_id = '$user_id'";
		$result = databaseQuery($query, $db_link);

		return $result;
	}

	/* END LOGIN */

	/* START REGISTER */

	// gets username to check if already exists
	function checkUsername($username, $db_link)
	{
		$query = "SELECT sw_user.user_name FROM sw_user WHERE sw_user.user_name = '" . $username . "'";
		$result = databaseQuery($query, $db_link);
		$row = databaseFetchRow($result);

		return $row;
	}

	// gets email to check if already in use
	function checkEmail($user_email, $db_link)
	{
		$query = "SELECT sw_user.user_email FROM sw_user WHERE sw_user.user_email = '" . $user_email . "'";
		$result = databaseQuery($query, $db_link);
		$row = databaseFetchRow($result);

		return $row;
	}

	function checkRegister($username, $user_firstname, $user_lastname, $user_email, $passwd, $passwd2, $db_link) {
		$errors = "";

		$checkUsername = checkUsername($username, $db_link);
		$checkEmail = checkEmail($user_email, $db_link);

		// Checks if username already exists.
		if(!empty($checkUsername))
		{
			foreach($checkUsername as $value)
			{
				if($value === $username)
				{
					$errors .= "Gebruikersnaam bestaat al. <br />";
				}
			}
		}

		// Email validation
		// Also checks if email already exists.
		if(!empty($checkEmail))
		{
			foreach($checkEmail as $value)
			{
				if($value === $user_email)
				{
					$errors .= "Email adres is al in gebruik. <br />";
				}
			}
		}

		// When there are no errors, validation complete and call createUser to register the new account.
		// When there are errors, the errors will be returned to show in the view.
		if(empty($errors))
		{
			createUser($username, $user_firstname, $user_lastname, $user_email, $passwd, $db_link);
			return true;
		}
		else
		{
			return $errors;
		}
	}

	function updateBlock($block_id, $block_name, $block_content, $user_id, $db_link) {
		$query = "UPDATE sw_block SET ";

		if($block_name != "") {
			$query .= "block_name='"  . $block_name . "', ";
		}
		if($block_content != "") {
			$query .= "block_content='" . $block_content . "', ";
		}
		$query .= "last_edit_user='" . $user_id . "' WHERE block_id = '" . $block_id . "'";
		$result = databaseQuery($query, $db_link);
		return $result;
	}

	function updateUserPermission($user_id, $rights, $group_id, $db_link) {
		$query = "UPDATE sw_user_group SET user_group_rights = '"  . $rights . "'  WHERE user_id = '" . $user_id . "' AND group_id = '" . $group_id . "'";
		$result = databaseQuery($query, $db_link);
		return $result;
	}
	function setUserTheme($user_id, $theme_id, $db_link) {
		$query = "UPDATE sw_user SET user_theme = '" . $theme_id . "' WHERE user_id = '" . $user_id . "'";
		$result = databaseQuery($query, $db_link);
		return $result;
	}

	function validateUser($validation_string, $db_link) {
		$success = false;
		$is_validatable = false;
		$query =   "SELECT *
					FROM sw_user
					WHERE user_validated = '" . $validation_string . "'";
		$result = databaseQuery($query, $db_link);
		while($row = databaseFetchRow($result)) {
			$is_validatable = true;
		}
		if($is_validatable) {
			$query = "UPDATE sw_user SET user_validated = '' WHERE user_validated = '" . $validation_string . "'";
			$result = databaseQuery($query, $db_link);
			if($result == false) {
				$success = false;
			}
			else {
				$success = true;
			}
		}

		return $success;
	}

	function addBlock($block_name, $block_content, $group_id, $user_id, $db_link) {
		$query = "INSERT INTO sw_block(block_name, block_content, group_id, last_edit_user) VALUES ('" . $block_name . "','" . $block_content . "','" . $group_id . "','" . $user_id . "')";
		$result = databaseQuery($query, $db_link);
		return $result;
	}

	function uploadRemovePath($filename){
		$pathInfo = pathinfo($filename);
		return $pathInfo['filename'].".".$pathInfo['extension'];
	}
	/*
		@param Get all uploads from a block from the database
		@param
		@param
	*/
	function getAllUploads($block_id, $db_link) {
		$upload_array  = array();
		$query = "SELECT * FROM  sw_upload WHERE block_id = '" . $block_id . "'";

		$result = databaseQuery($query, $db_link);

		if(databaseNumRows($result) <= 0) {
			return false;
		}

		while($row = databaseFetchRow($result))
		{
			$upload_array[] = $row;
		}

		return $upload_array;
	}
	/* END REGISTER */


?>
