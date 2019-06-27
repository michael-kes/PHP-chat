// Validations on input to create realtime validation
function userNameValidate() 
{
	var regexUsername = /^[a-zA-Z\d]{2,40}$/;

	// Adds class "invalid" when regex does not match
	if(!regexUsername.test(loginForm.username.value)) 
	{
		$("#username").addClass("invalid");
		return false;
	}
	// Adds class "valid" when regex does match
	else 
	{
		$("#username").removeClass("invalid").addClass("valid");
	}
}

function userFirstNameValidate() 
{
	var regexFirstname = /^[a-zA-Z]{2,40}$/;

	if(!regexFirstname.test(loginForm.firstname.value)) 
	{
		$("#firstname").addClass("invalid");
		return false;
	}
	else 
	{
		$("#firstname").removeClass("invalid").addClass("valid");
	}
}

function userLastNameValidate() 
{
	var regexLastname = /^[a-zA-Z ]{2,40}$/;

	if(!regexLastname.test(loginForm.lastname.value)) 
	{
		$("#lastname").addClass("invalid");
		return false;
	}
	else 
	{
		$("#lastname").removeClass("invalid").addClass("valid");
	}
}

function emailValidate() 
{
	var regexEmail = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

	if(!regexEmail.test(loginForm.email.value)) 
	{
		$("#email").addClass("invalid");
		return false;
	}
	else 
	{
		$("#email").removeClass("invalid").addClass("valid");
	}
}

function passwordValidate() 
{
	var regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/;

	if(!regexPassword.test(loginForm.password.value)) 
	{
		$("#key").addClass("invalid");
		return false;
	}
	else 
	{
		$("#key").removeClass("invalid").addClass("valid");
	}
}

function passwordCheckValidate() 
{
	if(loginForm.password.value != loginForm.passwordCheck.value) 
	{
    	$("#keyCheck").addClass("invalid");
    	return false;
  	}
  	else 
  	{
    	$("#keyCheck").removeClass("invalid").addClass("valid");
	}
}

function passwordValidateChats() 
{
	var regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/;

	if(!regexPassword.test(form2.groupPassword.value)) 
	{
		$("#groupPassword").addClass("invalid");
		return false;
	}
	else 
	{
		$("#groupPassword").removeClass("invalid").addClass("valid");
	}
}

function passwordValidateEditChats() 
{
	var regexPassword = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/;

	if(!regexPassword.test(form1.groupPassword.value)) 
	{
		$("#groupPassword").addClass("invalid");
		return false;
	}
	else 
	{
		$("#groupPassword").removeClass("invalid").addClass("valid");
	}
}

function passwordCheckEditChatsValidate() 
{
	if(form1.groupPassword.value != form1.groupPasswordCheck.value) 
	{
    	$("#groupPasswordCheck").addClass("invalid");
    	return false;
  	}
  	else 
  	{
    	$("#groupPasswordCheck").removeClass("invalid").addClass("valid");
	}
}

// Submit validation
function registerFormCheck(loginForm) 
{
	// If any functions return false and therefor do not correctly validate, does not allow form to be send.
	if(userNameValidate() === false || userFirstNameValidate() === false || userLastNameValidate() === false || emailValidate() === false || passwordValidate() === false || passwordCheckValidate() === false)
	{
		return false;
	}
	// Allows form to send if validated
	else 
	{
		return true;
	}

}
