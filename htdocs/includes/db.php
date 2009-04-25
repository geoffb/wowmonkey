<?php

$database;

function DB_GetConnection()
{
	$user = "wowmonkey";
	$pass = "wowmonkey2006";
	$db = "wowmonkey";

	$mysqli = @new mysqli('mysql.wowmonkey.net', $user, $pass, $db);
	if (mysqli_connect_errno()) {
		die("Could not connect to the database.");
		exit;
	}
	return $mysqli;
}

function prep_str($str)
{
	if(strlen($str) == 0)
	{
		return "NULL";
	}
	else
	{
		$str = str_replace("'", "''", $str);
		return "'".$str."'";
	}
}

?>
