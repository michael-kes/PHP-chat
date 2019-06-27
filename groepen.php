
<?php include "includes/functions.php"; ?>
<?php include "includes/db.php"; ?>
<?php include "config.php"; ?>
<?php //include "common.php"; ?>

<?php session_start(); ?>

<?php
	// check is user is already logged in, else redirct to login page
    if (!isset($_SESSION['user_id']))
    {
        header("Location: index.php");
    }
?>

<?php
	$groupsArray = getChannelGroups();
	
	//$user_perm = getUserPermission($_SESSION['user_id']);

	$error_color = "green";
	$errors = "";

	/*
	 * Subscribe to private group
	 */
	if(isset($_POST['joinPrivateGroup']))
	{
		if(isset($_POST['password']))
		{
			$grid = escapeString($_POST['groupID']); // save sent groupid
			$submittedPwd = escapeString($_POST['password']); // save sent password

			$groupData = getPrivateGroup($grid);
			$password = $groupData['group_password'];
			$gid = $groupData['group_id'];
			$groupRights = 1;
			$subbed = subscribedGroup($_SESSION['user_id'], $gid);
			$verifyHashpassword = verifyPassword($submittedPwd, $password);

			$usersAlreadyInGroup = getAllSubscribersFromGroup($grid);
		    if($usersAlreadyInGroup == false)
		    {
		  		$groupRights = 1;
		    }
		    else 
		    {
		      $groupRights = 2;
		    }

			if($verifyHashpassword != false)
			{ // if passwords are equal
				if($subbed == false)
				{
					signupPrivateGroup($gid, $_SESSION['user_id'], $groupRights);
					$errors .= "U bent succesvol aangemeld voor deze groepschat!";
				}
				else
				{
					$errors .= "U heeft zich al aangemeld voor deze groepschat!";
					$error_color = "red";
				}
			}
			else
			{
				$errors .= "Wachtwoord is onjuist!";
				$error_color = "red";
			}

		}
	}

	/**
	 * Subscribe to public group
	 */
	if(isset($_POST['joinOpenGroup']))
	{
		// Execute signup functions
		$grid = escapeString($_POST['groupIDPublic']); // save sent groupid
		//$groupData = getPublicGroup($grid);
		//$gid = $groupData['group_id'];
	    $usersAlreadyInGroup = getAllSubscribersFromGroup($grid);
	    if($usersAlreadyInGroup == false)
	    {
	  		$groupRights = 1;
	    }
	    else 
	    {
	      $groupRights = 2;
	    }

		$subbed = subscribedGroup($_SESSION['user_id'], $grid);

		if($subbed == false)
		{
			signupPrivateGroup($grid, $_SESSION['user_id'], $groupRights);
			$errors .= "U bent succesvol aangemeld voor deze groepschat!";
		}
		else
		{
			$errors .= "U heeft zich al aangemeld voor deze groepschat!";
			$error_color = "red";
		}
	}

	/*
	 * Unsubscribe to a group
	 */
	if(isset($_POST['unsubGroup']))
	{
		// Run the unsub query/function in here
		$grid = escapeString($_POST['groupIDUnsub']); // save sent groupid
		unsubscribeGroup($_SESSION['user_id'], $grid);
		header("Location: groepen.php");
		exit();
	}

	/*
	 * Create new group
	 */
	if(isset($_POST['submitNewGroup']))
	{
		if(!empty($_POST['groupName']) && !empty($_POST['groupDescription']) && !empty($_POST['groupPassword']))
		{
			$groupName = escapeString($_POST['groupName']);
			$groupDescription = escapeString($_POST['groupDescription']);
			$groupPassword = escapeString($_POST['groupPassword']);

			$hashpassword = generateHash($groupPassword);

			if (groupnameExists($groupName))
			{
				$errors .= "Groepschat bestaat al!";
				$error_color = "red";
			}
			else
			{
				createGroup($groupName, $groupDescription, $hashpassword, $_SESSION['username']);
        
        		$last_id = $connection->insert_id;
      			$groupRights = 1;
        		signupPrivateGroup($last_id, $_SESSION['user_id'], $groupRights);
        
				header("Location: groepen.php");
			}
		}
		elseif(!empty($_POST['groupName']) && !empty($_POST['groupDescription']) && empty($_POST['groupPassword']))
		{
			$groupName = escapeString($_POST['groupName']);
			$groupDescription = escapeString($_POST['groupDescription']);
			$groupPassword = "";

			$hashpassword = generateHash($groupPassword);

			createGroup($groupName, $groupDescription, $groupPassword, $_SESSION['username']);
		    
		    $last_id = $connection->insert_id;
		    $groupRights = 1;
		    signupPrivateGroup($last_id, $_SESSION['user_id'], $groupRights);

			header("Location: groepen.php");
		}
		else
		{
			$errors .= "Groepschat naam en omschrijving zijn verplicht!";
			$error_color = "red";
		}
	}

	/*
	 * Delete group as admin
	 */
	if(isset($_POST['deleteGroup']) == 'emptyPassword')
	{
		$grid = escapeString($_POST['groupIDDelete']); // save sent groupid
		deleteGroup($grid);

		header("Location: groepen.php");

		//header("Location: " . BASE_URL . "kanaal/" . $channel_id);
	}
?>
	<?php include "includes/header.php"; ?>
	<?php include "includes/navbar.php"; ?>

	<div class="container">
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

		<div class="row">
			<div class="col s12">
				<div class="row">
					<div class="col s12 pageHeadColumn">
						<div class="row">
							<div class="col s6">
								<h5 class="teal-text">Groepschats</h5>
							</div>
							<div class="col s6" style="text-align: right;">
								<a style="margin: 0.82rem 0 0.656rem 0;"  class="waves-effect waves-light btn-floating hide-on-large-only <?php echo $core_colors['accent']; ?> modal-trigger modalButton2" data-target="modal2">
									<i class="material-icons left">edit</i>Groepschat aanmaken
								</a>
								<a style="margin: 0.82rem 0 0.656rem 0;"  class="waves-effect hide-on-med-and-down hide-on-small-only waves-light btn <?php echo $core_colors['accent']; ?> modal-trigger modalButton2" data-target="modal2">
									<i class="material-icons left">edit</i>Groepschat aanmaken
								</a>
							</div>
						</div>
					</div>

					<?php foreach((array)$groupsArray as $key => $groups): ?>
						<?php $subscribed = subscribedGroup($_SESSION['user_id'], $groups['group_id']); 
							  $groupMembersArray = getAllSubscribersFromGroup($groups['group_id']);
							  $groupMemberCount = count($groupMembersArray);
							  $ownGroupPermission = deleteGroupPermission($groups['group_id'], escapeString($_SESSION['user_id'])); ?>
						<div class="col s12 m6 l6">
						<?php if ($groups != false) { ?>
							<div class="card white darken-1 hoverable">
								<div class="card-content black-text" style="height: 200px; position: relative;">
									<span class="card-title"><?php echo $groups['group_name']; ?></span>

									<?php
										if($ownGroupPermission == escapeString($_SESSION['user_id']))
										{ ?>
											<i class="small material-icons right-align modalDeleteButton clickableDiv tooltipped" data-position="top" data-tooltip="Klik hier om de groepschat te verwijderen" style="float: right; margin-top: 10px;" data-deletegroupid="<?php echo $groups['group_id']; ?>">delete</i>
										<?php }
									?>			

									<i class="small material-icons right-align tooltipped" data-position="top" data-tooltip="<?php echo $groups['group_password'] === "" ? 'Open groepschat' : 'Gesloten groepschat'; ?>" style="float: right; margin-top: 10px;"><?php echo $groups['group_password'] === "" ? 'lock_open' : 'lock_outline'; ?></i>	

									<p><?php echo $groups['group_description']; ?></p>

									<span style="font-size: 14px; position: absolute; bottom: 0; right: 20px; float: right;"><b><?php echo $groupMemberCount; ?></b>/8 deelnemer(s)</span>
									

								</div>
								<div class="card-action">
									<?php
									if($subscribed == false)
									{
										if ($groupMemberCount != 8)
										{
											echo $groups['group_password'] === "" ? '<button class="btn ' . $core_colors['main'] . ' modal-trigger modalButtonPublic" data-target="modalPublic" data-publicid="' . $groups['group_id'] . '">Aansluiten</button>'
											: '<button class="btn ' . $core_colors['main'] . ' modal-trigger modalButton" data-target="modal1" data-groupid="' . $groups['group_id'] . '">Aansluiten</button>';
										}
										else
										{
											echo $groups['group_password'] === "" ? '<button class="btn ' . $core_colors['main'] . ' disabled" >Groepschat is vol</button>'
											: '<button class="btn ' . $core_colors['main'] . ' disabled">Groepschat is vol</button>';
										}
										
									}
									else
									{
										echo '<a class="waves-effect waves-light btn" href="groep.php?groep=' . $groups['group_id'] . '">Openen</a>';
										if($ownGroupPermission != escapeString($_SESSION['username']))
										{ 
											echo '<a style="color: #F44336; font-weight: bold;" class="card-title unsub modalUnsubButton clickableDiv ' . $core_colors['accent'] . '-text" data-target="modalUnsub" data-unsubid="' . $groups['group_id'] . '">Afmelden</a>';
										}
									}
									?>
								</div>
							</div>
							<?php }
							else
							{ ?>
								<div class="row errorRow">
									<div class="col s12">&nbsp;</div>
										<div class="col s12 center-align">
											<div class="card white red-text text-darken-2" style="padding: 16px 0;">
												Er zijn nog geen groepschats aangemaakt
											</div>
										</div>
									<div class="col s12">&nbsp;</div>
								</div>
							<?php } ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>
	</div>

<!-- Modal Create group -->
<div id="modal2" class="modal">
	  <div class="modal-content">
	    <h4>Maak een groepschat</h4>
		    <p><b>Voer de benodidge gegevens in</b></p>
	      <div class="form-data">
    			<form method="post" id="form2" action="groepen.php">
    				<input type="text" id="groupName" name="groupName" placeholder="Groepschat naam"/>
    				<input type="text" id="groupDescription" name="groupDescription" length="190" placeholder="Groepschat omschrijving"/>
    				<i class="material-icons prefix clickableDiv tooltipped" data-position="top" data-tooltip="Laat de wachtwoord leeg voor een open groepschat">info</i>
    				<input type="password" id="groupPassword" name="groupPassword" placeholder="Groepschat wachtwoord"/>
    			</form>
    		</div>
      <br>
    <div class="modal-footer">
			<button class="btn waves-effect waves-light <?php echo $core_colors['accent']; ?>" data-dismiss="modal2" form="form2" type="submit" name="submitNewGroup">Aanmaken

			<i class="material-icons right">send</i>
			</button>
		</div>
	</div>
</div>

<!-- Modal Join private group -->
<div id="modal1" class="modal">
	  <div class="modal-content">
	    <h4>Aansluiten groepschat</h4>
		<p>Dit is een prive groepschat. Vul het wachtwoord in om u aan te sluiten.</p>
		<div class="form-data">
				<form method="post" id="form1">
					<input type="password" id="groupPwd" name="password" placeholder="Groepschat wachtwoord"/>
					<input type="hidden" id="groupValue" name="groupID" value="0"/>
				</form>
		</div>
		<br><div class="modal-footer">
			<button class="btn <?php echo $core_colors['main']; ?> colo waves-effect waves-light" data-dismiss="modal1" form="form1" type="submit" name="joinPrivateGroup">Aanmelden
		    <i class="material-icons right">send</i>
		  </button>
		</div>
	</div>
</div>

<!-- Modal Join public group -->
<div id="modalPublic" class="modal">
	  <div class="modal-content">
	    <h4>Aansluiten groepschat</h4>
		<p>Dit is een open Groepschat. Om u aan te melden, klik dan op de aanmeld knop hier beneden.</p>
		<div class="form-data">
			<form method="post" id="form3">
				<input type="hidden" id="groupValuePublic" name="groupIDPublic" value="0"/>
			</form>
		</div>
		<br><div class="modal-footer">
			<button class="btn <?php echo $core_colors['main']; ?> waves-effect waves-light" data-dismiss="modalPublic" form="form3" type="submit" name="joinOpenGroup">Aanmelden
		    <i class="material-icons right">send</i>
		  </button>
		</div>
	</div>
</div>

<!-- Modal delete group -->
<div id="modalDelete" class="modal">
	  <div class="modal-content">
	    <h4>Groepschat verwijderen</h4>
		<p>Weet u zeker dat u deze groepschat wilt verwijderen?</p>
		<div class="form-data">
			<form method="post" id="form4">
				<input type="hidden" id="groupDelete" name="groupIDDelete" value="0"/>
			</form>
		</div>
		<br><div class="modal-footer">
			<button class="btn <?php echo $core_colors['accent']; ?> waves-effect waves-light" data-dismiss="modalDelete" form="form4" type="submit" name="deleteGroup">Verwijderen
		    <i class="material-icons right">send</i>
		  </button>
		</div>
	</div>
</div>

<!-- Modal unsubscribe group -->
<div id="modalUnsub" class="modal">
	  <div class="modal-content">
	    <h4>Groepschat afmelden</h4>
		<p>Weet u zeker dat u zich wilt afmelden?</p>
		<div class="form-data">
			<form method="post" id="form5">
				<input type="hidden" id="groupUnsub" name="groupIDUnsub" value="0"/>
			</form>
		</div>
		<br><div class="modal-footer">
			<button class="btn <?php echo $core_colors['accent']; ?> waves-effect waves-light" data-dismiss="modalUnsub" form="form5" type="submit" name="unsubGroup">Afmelden
		    <i class="material-icons right">send</i>
		  </button>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$(".modalButton").on("click", function(e) {
		$('#modal1').openModal();
		$('#groupValue').val($(this).attr('data-groupid'));
		// Make password field empty
		$('.modal-content').find("input[type=password]").val("");
	});

	$(".modalButtonPublic").on("click", function(e) {
		$('#modalPublic').openModal();
		$('#groupValuePublic').val($(this).attr('data-publicid'));
	});

	$(".modalButton2").on("click", function(e) {
		$('#modal2').openModal();
	});

	$(".modalDeleteButton").on("click", function(e) {
		$('#modalDelete').openModal();
		$('#groupDelete').val($(this).attr('data-deletegroupid'));
	})

	$(".modalUnsubButton").on("click", function(e) {
		$('#modalUnsub').openModal();
		$('#groupUnsub').val($(this).attr('data-unsubid'));
	})

	$(".modalEditButton").on("click", function(e) {
		$('#modalEdit').openModal();
		$('#channelEdit').val($(this).attr('data-editchannelid'));
	})

	$('.tooltipped').tooltip({delay: 500});
});

	document.getElementById("groupPassword").oninput = function() {passwordValidateChats()};
</script>

<?php include "includes/footer.php"; ?>
