<?php include "includes/functions.php"; ?>
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
$group_id = $_GET['groep'];

	// Check if user is allowed to view this group
$user_perm = getUserPermission($_SESSION['user_id'], $group_id);
if($user_perm === false) {
	header("Location: groepen.php");
	exit();
}

$groupMembersArray = getAllSubscribersFromGroup($group_id);
$groupMemberCount = count($groupMembersArray);
$usernamesArray = getUsernames($group_id);
$group_item = getSingleGroup($group_id);
$chat_items = getAllChats($group_id);
$showAdmin = showAdmin($group_id);

$error_color = "green";
$errors = "";

	/*
	 * Edit Group
	 */
	if(isset($_POST['editGroup']))
	{
		if(isset($_POST['groupName']) && isset($_POST['groupDescription']))
		{
			if($user_perm != false && $user_perm == 1) 
			{
				$grid = escapeString($_POST['groupIDEdit']);

				$groupName = escapeString($_POST['groupName']);
				$groupDescription = escapeString($_POST['groupDescription']);
				$groupPassword = escapeString($_POST['groupPassword']);
				$groupPasswordCheck = escapeString($_POST['groupPasswordCheck']);

				$hashpassword = generateHash($groupPassword);

				if (!empty($groupName) && !empty($groupDescription)) 
				{
					if (groupnameExists($groupName))
					{
						$errors .= "Groepschat bestaat al!";
						$error_color = "red";
					}
					else
					{
						if($groupPassword == $groupPasswordCheck)
						{
							editGroup($grid, $groupName, $groupDescription, $hashpassword);

							$errors .= "U heeft de groepschat succesvol gewijzigd!";
						}
						else
						{
							$errors .= "Wachtwoorden komen niet overeen!";
							$error_color = "red";
						}

						if(isset($_POST['checkPassword']) && isset($_POST['checkPassword']) == "emptyPassword")
						{
							editGroupPassword($grid);
						}
					}
					
				}
				else
				{
					$errors .= "Groepschat naam en omschrijving zijn verplicht!";
					$error_color = "red";
				}
				
			}
			else
			{
				$errors .= "U heeft geen rechten tot deze actie!";
				$error_color = "red";
			}
		}
	}
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
								<h5 class="teal-text"><b>Groepschat:</b> <?php echo $group_item['group_name']; ?></h5>
							</div>
							<div class="col s6" style="text-align: right;">
								<a class="rightwaves-effect waves-light btn-floating hide-on-large-only modal-trigger <?php echo $core_colors['accent']; ?>" href="#modal1" style="margin: 0.82rem 0 0.656rem 0;">
									<i class="material-icons" style="vertical-align: bottom;">info_outline</i> Groepschat informatie
								</a>
								<a class="rightwaves-effect waves-light hide-on-med-and-down hide-on-small-only btn modal-trigger <?php echo $core_colors['accent']; ?>" href="#modal1" style="margin: 0.82rem 0 0.656rem 0;">
									<i class="material-icons" style="vertical-align: bottom;">info_outline</i> Groepschat informatie
								</a>
							</div>
						</div>
					</div>
				</div>

				<div id="modal1" class="modal">
					<div class="modal-content">
						<div class="row">
							<div class="col s6 left-align">
								<h4><?php echo '<div class="italic">Aantal deelnemers: ' . $groupMemberCount . '</div>'; ?></h4>
							</div>
							<div class="col s6 right-align">
								<?php
								if($user_perm == 1)
								{
									echo '<a class="waves-effect hide-on-large-only waves-light btn-floating ' . $core_colors['accent'] . ' modal-trigger modalEditButton" data-target="modalEdit" data-editgroupid="' . $group_id . '"><i class="material-icons left">edit</i>Aanpassen</a>';

									echo '<a class="waves-effect waves-light btn ' . $core_colors['accent'] . ' modal-trigger modalEditButton hide-on-med-and-down hide-on-small-only" data-target="modalEdit" data-editgroupid="' . $group_id . '"><i class="material-icons left">edit</i>Aanpassen</a>';
								}
								?>
							</div>
						</div>

						<?php
						if($groupMemberCount > 0) 
						{
							echo '<ul class="collection with-header">';

							if($user_perm == 1)
							{
								foreach ($groupMembersArray as $key=>$member)
								{											
									if($showAdmin == $member["user_name"])
									{
										echo '<li class="collection-item left" style="width: 100%;">'. $member["user_name"] .'<a href="" class="right green-text" style="pointer-events:none;">Admin</a></li>';
									}
									else
									{
										echo '<li class="collection-item left" style="width: 100%;">'. $member["user_name"] .'<a href="deleteuser.php?groep=' . $member['group_id'] . '&delete=' . $member["user_id"] . '" class="right red-text">Verwijderen</a></li>';	
									}
								}
								echo '</ul>';
							}
							else
							{
								foreach ($groupMembersArray as $key=>$member)
								{
									if($showAdmin == $member["user_name"])
									{
										echo '<li class="collection-item">'. $member["user_name"] .'<a href="" class="right green-text" style="pointer-events:none;">Admin</a></li>';
									}
									else
									{
										echo '<li class="collection-item">'. $member["user_name"] .'</li>';
									}
								}
								echo '</ul>';
							}
						}
						else
						{
							echo '<div class="italic">Deze groepschat heeft nog geen deelnemers!</div>';
						}
						?>

					</div>
				</div>

				<div id="chatHistoryContainer">
				</div>

				<div id="chatSendContainer" style="position: absolute; bottom: 0px; left: 0px; width: 100%; box-sizing: border-box; margin-bottom: 16px;">
					<form name="addMessage" method="post" action="groep.php">
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

	<!-- Modal Edit group info -->
	<div id="modalEdit" class="modal">
		<div class="modal-content">
			<h4>Pas hier de groepschat aan</h4>
			<p><b>Voer de benodidge gegevens in</b></p>
			<div class="form-data">
				<form method="post" id="form1">
					<input type="text" id="groupName" name="groupName" value="<?php echo $group_item['group_name']; ?>" placeholder="Groepschat naam"/>
					<input type="text" id="groupDescription" name="groupDescription" value="<?php echo $group_item['group_description']; ?>" length="190" placeholder="Groepschat omschrijving"/>
					<i class="material-icons prefix clickableDiv tooltipped" data-position="top" data-tooltip="Laat de wachtwoord leeg voor een open groepschat">info</i>
					<input type="password" id="groupPassword" name="groupPassword" placeholder="Groepschat wachtwoord"/>
					<input type="password" id="groupPasswordCheck" name="groupPasswordCheck" placeholder="Herhaal uw wachtwoord"/>
					<input type="hidden" id="groupEdit" name="groupIDEdit" value="0"/>
				</form>
			</div>
			<div class="modal-footer">
				<br>
				<button class="btn <?php echo $core_colors['main']; ?> waves-effect waves-light" data-dismiss="modalEdit" form="form1" type="submit" name="editGroup">Wijzigen
					<i class="material-icons right">send</i>
				</button>
				<input type="checkbox" name="checkPassword" value="emptyPassword" class="left filled-in" id="filled-in-box" form="form1" />
				<label for="filled-in-box">Wachtwoord verwijderen</label>
			</div>
		</div>
	</div>

	<script>
		document.getElementById("groupPassword").oninput = function() {passwordValidateEditChats()};
		document.getElementById("groupPasswordCheck").oninput = function() {passwordCheckEditChatsValidate()};

		var this_user = "<?php echo $_SESSION['user_id']; ?>";
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
					url: 'includes/add-message.php',
					type: 'POST',
					data: {
						"message": newMessage,
						"submit": "true",
						"group_id": "<?php echo $group_id; ?>",
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
			var url = "includes/getchat.php";
			$.ajax({
				url: url,
				type: 'GET',
				data: { "group_id": "<?php echo $group_id; ?>" },
				success: function (data) {
					if(data == false)
					{
						var no_chat_msg = '<div class="col s12 center-align"><div class="card white red-text text-darken-2 errorCard" style="padding: 16px 0;">Er zijn nog geen berichten in deze Groepschat!</div></div></div>';

						document.getElementById('chatHistoryContainer').innerHTML = no_chat_msg;
					}
					else
					{
						loadData(data);
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
