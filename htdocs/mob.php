<?php

include "includes/common.php";

if(!empty($_REQUEST["i"]))
{
	$id = $_REQUEST["i"];
}
else
{
	die("No mob specified!");
}

//get item data
$db = DB_GetConnection();
$sql = sprintf("select * from mob where mob_id = %s", $id);
$mob = $db->query($sql);
$db->close();
$d = $mob->fetch_assoc();

$skin = new skin("mob.skn");
$skin->setPageTitle(sprintf("Mob: %s", $d["mob_name"]));

$skin->token("MOB_NAME", $d["mob_name"]);
$title = $d["mob_description"];
if(strlen($title) > 0 )
	$title = "&lt;".$title."&gt;";
$skin->token("MOB_TITLE", $title);

$skin->dump();

$mob->close();
?>