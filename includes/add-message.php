<?php include "db.php"; ?>
<?php include "functions.php"; ?>
<?php include "../config.php"; ?>

<?php session_start(); ?>

<?php
if (isset($_POST['submit']) && isset($_POST['message']) && isset($_POST['group_id']))
{
	$message = escapeString($_POST['message']);
	$user_id = escapeString($_SESSION['user_id']);
	$group_id = escapeString($_POST['group_id']);

	$encrypt = base64_encode($message);

	$newMessage = addMessage('', $encrypt, $user_id, $group_id);
	//var_dump($newMessage);
}
?>
