<?php

// arrays with keys
$db['db_host'] = "localhost";
$db['db_user'] = "root";
$db['db_pass'] = "";
$db['db_name'] = "secure_programming";

//db info to constant so the values cant be changed or use global
foreach($db as $key => $value)
{
	define(strtoupper($key), $value);
}

$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

/*if($connection)
{
	echo "Database connected";
}
else
{
	echo "Database not connected";
}*/

?>