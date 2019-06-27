<?php 
function databaseConnect($host = '', $user = '', $pass = '', $db = '') {
	$link = mysqli_connect($host, $user, $pass, $db);
	if (!$link) { 
		trigger_error(mysqli_connect_error()); 
		echo "Database connectie mislukt!";
		die();
	}
	if ($link->connect_errno) {
		echo "Database connectie mislukt!";
		die();
	}
	mysqli_set_charset($link, 'utf8');
	return $link;
}

function databaseQuery($sql, $link = null) {
	$result = mysqli_query($link, $sql);	
	if ($result === false) {
		
		$backtrace = debug_backtrace();
		trigger_error(
			'<br>' . 'Error in file: '  
			. $backtrace[0]['file'] . ' on line '  .  $backtrace[0]['line'] 
			. '<br>' . 'Error in query:' . $sql
			);
	}

	return $result;
}

function databaseFetchRow($result) {
	return mysqli_fetch_assoc($result);
}

function databaseNumRows($result) {
	if ($result) {
		$num_rows = (int)mysqli_num_rows($result);
		return $num_rows;
	}
	return 0;
}
function databaseResult($result, $column) {
	$row = databaseFetchRow($result);
	return $row[$column];
}
?>