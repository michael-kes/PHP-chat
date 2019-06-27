$(document).ready(function(){
	$('.button-collapse').sideNav({
		menuWidth: 340,
		edge: 'left',
		closeOnClick: true
	});
	$('.modal-trigger').leanModal();

	$('.collapsible').collapsible({
		accordion : false
	});
	$('#sideMenuChannelSearchDelete').click(function() {
		$('#sideMenuChannelSearch').val("");
		sideMenuSearch("");
	});
	$('#sideMenuChannelSearch').on('keyup paste', function() {
		sideMenuSearch($(this).val());
	});
	$('#sideMenuChannels li.collection-item').click(function(e) {
		if($(e.target).is('.sideMenuChannelItem') ) {
			e.preventDefault();
			window.location.replace($(this).attr('data-href'));
		}
	});
	$('.errorCard').click(function(){
		console.log($(this).parent().parent());
		$(this).parent().parent().slideUp();
	});
});

function sideMenuSearch(searchString) {
	var searchStringL = searchString.toLowerCase();
	if(searchString == "") {
		$('#sideMenuChannels').children('li').each(function () {
			$(this).show();
		});
	}
	else {
		$('#sideMenuChannels').children('li').each(function () {
			var channelDiv = $(this);
			var hideThisDiv = true;
			var channelName = $(this).find('.sideMenuChannelItemName').html().toLowerCase();
			$(this).find('.channelGroups').children('a').each(function() {
				var groupName = $(this).text().toLowerCase();
				if(groupName.indexOf(searchStringL) >= 0) {
					hideThisDiv = false;
					channelDiv.find('.groupCollapse:not(.active)').trigger('click');
				}
			});
			if(channelName.indexOf(searchStringL) >= 0) {
				hideThisDiv = false;
			}
			if(hideThisDiv) {
				$(channelDiv).hide();
			}
		});
	}
}

function channelNameValidate() {
	var regexChannelname = /^[a-zA-Z\d ]{2,40}$/;

	// Validate Channelname
	if(!regexChannelname.test(form2.channel_name.value)) {
		$("#channel_name").addClass("invalid");
		return false;
	}
	else {
		$("#channel_name").removeClass("invalid").addClass("valid");
	}
}

function channelDescriptionValidate() {
	var regexChannelDescription = /^[a-zA-Z\d ]{4,190}$/;

	// Validate Channel Description
	if(!regexChannelDescription.test(form2.channel_description.value)) {
		$("#channel_description").addClass("invalid");
		return false;
	}
	else {
		$("#channel_description").removeClass("invalid").addClass("valid");
	}
}

function channelPasswordValidate() {
	var regexChannelPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/;

	// Validate password, but only when filled
	if(form2.channel_password.value) {
		// Check password
		if(!regexChannelPassword.test(form2.channel_password.value)) {
			$("#channel_password").addClass("invalid");
			return false;
		}
		else {
			$("#channel_password").removeClass("invalid").addClass("valid");
		}
	}
}

function channelPasswordCheckValidate() {
	// Validate password check, but only when field is filled
	if(form2.channel_password.value)
	{
		// Check if second password equals password
		if(form2.channel_password.value != form2.channel_password_check.value) {
			$("#channel_password_check").addClass("invalid");
			return false;
		}
		else {
			$("#channel_password_check").removeClass("invalid").addClass("valid");
		}
	}
}

function groupNameValidate() {
	var regexGroupname = /^[a-zA-Z\d ]{2,40}$/;

	// Validate Groupname
	if(!regexGroupname.test(form2.group_name.value)) {
		$("#group_name").addClass("invalid");
		return false;
	}
	else {
		$("#group_name").removeClass("invalid").addClass("valid");
	}
}

function groupDescriptionValidate() {
	var regexGroupDescription = /^[a-zA-Z\d ]{4,190}$/;

	// Validate Group description
	if(!regexGroupDescription.test(form2.group_description.value)) {
		$("#group_description").addClass("invalid");
		return false;
	}
	else {
		$("#group_description").removeClass("invalid").addClass("valid");
	}
}

// Form validation for Channels
function channelFormCheck(form2)
{
	// Regex for Channelname
	/*var regexChannelname = /^[a-zA-Z\d ]{2,40}$/;
	var regexChannelDescription = /^[a-zA-Z\d ]{4,190}$/;
	var regexChannelPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/;
	var regexGroupname = /^[a-zA-Z\d]{2,40}$/;
	var regexGroupDescription = /^[a-zA-Z\d ]{4,190}$/;

	// Validate Channelname
	if(!regexChannelname.test(form2.channel_name.value)) {
		$("#channel_name").addClass("invalid");
		return false;
	}
	else {
		$("#channel_name").removeClass("invalid").addClass("valid");
	}
	// Validate Channel Description
	if(!regexChannelDescription.test(form2.channel_description.value)) {
		$("#channel_description").addClass("invalid");
		return false;
	}
	else {
		$("#channel_description").removeClass("invalid").addClass("valid");
	}
	// Validate password, but only when filled
	if(form2.channel_password.value) {
		// Check password
		if(!regexChannelPassword.test(form2.channel_password.value)) {
			$("#channel_password").addClass("invalid");
			return false;
		}
		else {
			$("#channel_password").removeClass("invalid").addClass("valid");
		}
		// Check if second password equals password
		if(form2.channel_password.value != form2.channel_password_check.value) {
			$("#channel_password_check").addClass("invalid");
			return false;
		}
		else {
			$("#channel_password_check").removeClass("invalid").addClass("valid");
		}
	}
	// Validate Groupname
	if(!regexGroupname.test(form2.group_name.value)) {
		$("#group_name").addClass("invalid");
		return false;
	}
	else {
		$("#group_name").removeClass("invalid").addClass("valid");
	}
	// Validate Group description
	if(!regexGroupDescription.test(form2.group_description.value)) {
		$("#group_description").addClass("invalid");
		return false;
	}
	else {
		$("#group_description").removeClass("invalid").addClass("valid");
	}*/

	if(channelNameValidate() === false || channelDescriptionValidate() === false || channelDescriptionValidate() === false || channelPasswordPassword() === false || groupNameValidate() === false || groupDescriptionValidate() === false)
	{
		return false;
	}
	else {
		return true;
	}
}
