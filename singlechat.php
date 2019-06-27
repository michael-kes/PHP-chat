<?php include "includes/functions.php"; ?>
<?php include "includes/functions.profile.php"; ?>
<?php include "includes/db.php"; ?>
<?php include "config.php"; ?>

<?php session_start(); ?>

<?php
if (!isset($_SESSION['user_id']))
{
	header("Location: index.php");
}
?>

<?php

$user_two = $_GET['user_two'];

$user_id = $_SESSION['user_id'];

$usernamesArray = getUsernames($user_id);
$chat_items = getAllDmChats($user_id);

$error_color = "green";
$errors = "";

?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container" style="height: 90%; width: 70%">

	<?php if($errors != ""): ?>
		<div class="row errorRow">
			<div class="col s3">&nbsp;</div>
			<div class="col s6 center-align">
				<div class="card white <?php echo $error_color; ?>-text text-darken-2 errorCard" style="padding: 16px 0;">
					<?php echo $errors; ?>
				</div>
			</div>
			<div class="col s3">&nbsp;</div>
		</div>
	<?php endif; ?>

	<div class="row" style="height: 100%; margin-bottom: 0;">
		<div class="col s12 m12" id="chatMainContainer">
			<div class="row" style="margin-bottom: 0;">
				<div class="col s12 pageHeadColumn">
					<div class="row">
						<div class="col s6">
							<h5 class="teal-text"><b>Direct Messaging with:</b></h5>
						</div>
					</div>
				</div>
			</div>
			<div id="chatHistoryContainer">
			</div>

			<div id="chatSendContainer" style="position: absolute; bottom: 0px; left: 0px; width: 100%; box-sizing: border-box; margin-bottom: 16px;">
				<form name="addDmMessage" method="post" action="singlechat.php">
					<div class="row" style="margin: 0;">
						<div class="valign-wrapper">
							<ul class="collapsible subscribers" data-collapsible="accordion">
							</ul>
							<div class="input-field col s12">
								<textarea class="materialize-textarea" id="chatTextarea" rows="4" name="message" style="min-height: 3em; height: 3em; border: 1px solid #e0e0e0; padding: 8px; background-color: #ffffff; margin: 0; max-height: 10em; border-radius: 5px;" placeholder="Typ hier..."></textarea>
							</div>
							<div class="col s2">
								<div id="chatSendButton" class="clickableDiv" name="submit" style="padding: 8px; border-radius: 50%; width: 60px; height: 60px; margin-top: 15px; float: right; border: 1px solid #e0e0e0; text-align: center;background-color: #ffffff; margin-right: 45px;">
									<i class="valign material-icons <?php echo $core_colors['accent']; ?>-text" style="font-size: 30px; line-height: 46px;">message</i>
								</div>
							</div>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>
</div>


<script>
	var this_user = "<?php echo $user_id; ?>";
	$(document).ready(function(){

		$(".modalEditButton").on("click", function(e) {
			$('#modalEdit').openModal();
			$('#groupEdit').val($(this).attr('data-editgroupid'));
		})

		$(".subscribers").empty();

		$('#chatSendButton').click(function(event) {
			sendChatMessage();
			event.preventDefault();
		});
		$('#chatTextarea').keydown(function(e) {
			if(e.keyCode == 13 && $(this).val().split("\n").length >= $(this).attr('rows')) {
				return false;
			}
			$('#chatMainContainer').css('padding-bottom', $('#chatSendContainer').outerHeight(true) + 'px');
			var scrollToPos = document.getElementById("chatHistoryContainer").scrollHeight;
		});

		$('.subscribers li').on("click", function(){
			console.log("testnu");
		});

		$('#chatTextarea').bind('input propertychange', function() {
			if(this.value.indexOf("@") > -1)
			{
				$(".subscribers").empty();
			 		//loadSubscribers();
			 	}
			 	else
			 	{
			 		$(".subscribers").empty();
			 	}
			 });

		loadAjax();

		setInterval(function()
		{
			loadAjax();
		}, 500);

			/*$('#closeModalButton').click(function() {
				$('#modalEdit').closeModal();
			});*/
		});

	function sendChatMessage() {
		var newMessage = $('#chatTextarea').val();
		if (newMessage == '')
		{
			alert("Je kan geen leeg bericht sturen");
		}
		else
		{
			$.ajax({
				url: 'includes/add-dm-message.php',
				type: 'POST',
				data: {
					"message": newMessage,
					"submit": "true",
					"user_two": "<?php echo $user_two; ?>",
				},
				success: function (data) {
					$('#chatTextarea').val('');
					loadAjax();
				}
			});
		}
	}

	function nl2br (str, is_xhtml) {
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
		return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ breakTag +'$2');
	}

		/*function loadSubscribers()
		{
			var url = "api/getSubscribers";

			$.ajax({
				url:'<?php echo BASE_URL; ?>' + url,
				type:'GET',
				data:{"group_id": "<?php echo $group_id; ?>"},
				success : function(data){
					$(".subscribers").append(data).show("fadeIn");
				}
			});
		}*/

		function loadAjax()
		{
			var url = "includes/get-dm-chat.php";
			$.ajax({
				url: url,
				type: 'GET',
				data: { "user_two": "<?php echo $user_two; ?>" },
				success: function (data) {
					if(data == false)
					{
						var no_chat_msg = '<div class="col s12 center-align"><div class="card white red-text text-darken-2 errorCard" style="padding: 16px 0;">Er zijn nog geen berichten in deze Direct Message!</div></div></div>';

						document.getElementById('chatHistoryContainer').innerHTML = no_chat_msg;
					}
					else
					{
						loadData(data);
						console.log(data);
					}
				},
			});
		}

		function loadData(response)
		{
			var items = JSON.parse(response);
			var i;
			var output = '';

			for(i = 0; i < items.length; i++)
			{
				var extra_class = '';
				if(this_user == items[i].user_id)
				{
					extra_class = 'Right';
				}
				var username = items[i].user_name;
				var chattime = items[i].format_time;
				var chatdate = items[i].format_date;
				var chatmessage = nl2br(items[i].chat_message, true);
				var color = items[i].color;
				output += '<div class="chatHistoryRow' + extra_class + '"><div class="chatHistoryBalloon black-text white"><span class="' + color + '">' + username + '</span></br>' + chatmessage + '</br></br><span style="font-size: 10px;">' + chattime + '&nbsp;&nbsp;&nbsp;' + chatdate + '</span></div></div>';
				/*<div class="chatRowDateContainer"><div class="chatRowTime">' + chattime + '</div><div class="chatRowDate">' + chatdate + '</div></div>*/
			}
			document.getElementById('chatHistoryContainer').innerHTML = output;
			//$('#chatHistoryContainer').animate($('#chatHistoryContainer').prop("scrollHeight"), 500);

			var timer = window.setInterval(function()
			{
				var elem = document.getElementById('chatHistoryContainer');
				elem.scrollTop = elem.scrollHeight;
				window.clearInterval(timer);
			}, 500);
		}
	</script>

	<?php include "includes/footer.php";?>
