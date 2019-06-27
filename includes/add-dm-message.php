<?php include "db.php"; ?>
<?php include "functions.php"; ?>
<?php include "functions.profile.php"; ?>
<?php include "../config.php"; ?>
<?php include "singlechat.php"; ?>

<?php session_start(); ?>

<?php
if (isset($_POST['submit']) && isset($_POST['message']) && isset($_POST['user_two']))
{

	$message = escapeString($_POST['message']);
	$user_one_id = escapeString($_SESSION['user_id']);
	$user_two_id = escapeString($_POST['user_two']);

	$encrypt = base64_encode($message);


	$newMessage = addDmMessage('', $encrypt, $user_one_id, $user_two_id);
	//var_dump($newMessage);
}
?>
