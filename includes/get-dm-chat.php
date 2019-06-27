<?php include "db.php"; ?>
<?php include "functions.php"; ?>
<?php include "functions.profile.php"; ?>
<?php include "../config.php"; ?>

<?php
$user_two = $_GET['user_two'];

if(isset($_GET['user_two']) && $_GET['user_two'] > 0)
{
	$user_two = $_GET['user_two'];
}

$chat_items = getAllDmChats($user_two);

if(!empty($chat_items) && $chat_items !== false)
{
	foreach($chat_items as $key=> $item)
	{

		srand($item['user_one_id']);
		$chat_items[$key]['color'] = $core_chat_colors[rand(1, (count($core_chat_colors) -1))];
		$chat_items[$key]['chat_message'] = base64_decode($item['chat_message']);
		$chat_items[$key]['format_time'] = strftime($core_timestamp_formats['chat_timestamp'], strtotime($item['timemessage']));
		$chat_items[$key]['format_date'] = date($core_timestamp_formats['chat_datestamp'], strtotime($item['timemessage']));

	}
	echo json_encode($chat_items);
}
else
{
	return false;
}
?>
