
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

			if($verifyHashpassword == true)
			{ // if passwords are equal
				if($subbed == false)
				{
					signupPrivateGroup($gid, $_SESSION['user_id'], $groupRights);
					$errors .= "U bent succesvol aangemeld voor deze groep!";
				}
				else
				{
					$errors .= "U heeft zich al aangemeld voor deze groep!";
					$error_color = "red";
				}
			}
			else
			{
				echo $verifyHashpassword;
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
		$groupRights = 1;
		$subbed = subscribedGroup($_SESSION['user_id'], $grid);

		if($subbed == false)
		{
			signupPrivateGroup($grid, $_SESSION['user_id'], $groupRights);
			$errors .= "U bent succesvol aangemeld voor deze groep!";
		}
		else
		{
			$errors .= "U heeft zich al aangemeld voor deze groep!";
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
				$errors .= "Groepsnaam bestaat al!";
				$error_color = "red";
			} 
			else
			{
				createGroup($groupName, $groupDescription, $hashpassword);

				header("Location: groepen.php");
			}			
		}
		elseif(!empty($_POST['groupName']) && !empty($_POST['groupDescription']) && empty($_POST['groupPassword']))
		{
			$groupName = escapeString($_POST['groupName']);
			$groupDescription = escapeString($_POST['groupDescription']);
			$groupPassword = escapeString($_POST['groupPassword']);

			$hashpassword = generateHash($groupPassword);

			createGroup($groupName, $groupDescription);

			header("Location: groepen.php");
		}
		else
		{
			$errors .= "Groepsnaam en omschrijving zijn verplicht!";
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
								
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>


	<div class="col s12 m6 l6">
		<div class="card white darken-1 hoverable">
			<div class="card-content black-text">
				<span class="card-title"><h5>Hallo, <?php echo $_SESSION['firstname'], "&nbsp;".$_SESSION['lastname']; ?></h5></span>
				<span class="right">Online sinds: <i></i></span>
				<div class="row">
					<form class="col s12" style="float:left;">
						<div class="row">
							<div class="input-field col s12">
								<input placeholder="<?PHP echo $_SESSION['username'];?>" id="username" type="text" class="validate" value="<?PHP echo $_SESSION['username'];?>">
								<label for="username">Username</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s6">
								<input placeholder="<?PHP echo $_SESSION['firstname'];?>" id="first_name" type="text" class="validate" value="<?PHP echo $_SESSION['firstname'];?>">
								<label for="first_name">Firstname</label>
							</div>
							<div class="input-field col s6">
								<input  placeholder="<?PHP echo $_SESSION['firstname'];?>" id="last_name" type="text" class="validate" value="<?PHP echo $_SESSION['lastname'];?>">
								<label for="last_name">Lastname</label>
							</div>
						</div>
						<div class="row">
							<div class="input-field col s12">
								<input placeholder="email" id="email" type="email" class="validate" value="<?PHP echo $_SESSION['email'];?>">
								<label for="email">Email</label>
							</div>
						</div>
						<div class="form-group">
							<input name="login" type="submit" class="btn btn-primary btn-block" value="Opslaan" style="float:left;">
							<input name="login" type="submit" class="btn btn-primary btn-block" value="Clear" style="float:right;">
						</div>
					</form>
				</div>

				<ul class="collapsible" data-collapsible="accordion">
					<li>

						<div class="collapsible-header"><i class="material-icons">view_list</i>Kanalen ()</div>
						
					</li>
				</ul>
				<a class="dropdown-theme waves-effect waves-light  btn <?php echo $core_colors['accent'];?>" href="#" data-activates='dropdown1'><i class="material-icons left">color_lens</i>Thema</a>



				<form method="post" id="formTheme">
					<input type="hidden" id="themeInput" value="0" name="theme_id"/>
				</form>
				<ul id='dropdown1' class='dropdown-content'>

				</ul>
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
	</script>

	<?php include "includes/footer.php"; ?>
