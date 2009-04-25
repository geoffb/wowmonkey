<?php

//quest.php

include "includes/common.php";

if(!empty($_REQUEST["i"]))
{
	$id = $_REQUEST["i"];
}
else
{
	die("No quest specified!");
}

$sql = sprintf("call sp_getquest(%s);", $id);

$db = DB_GetConnection();
if($db->multi_query($sql))
{
	$result = $db->store_result();
}
$db->close();

$d = $result->fetch_assoc();

$skin = new skin("quest.skn");

$skin->token("QUEST_NAME", $d["quest_name"]);
$skin->token("QUEST_DESCRIPTION", $d["quest_description"]);
$skin->token("LEVEL", $d["quest_level"]);
$text = $d["quest_details"];
$text = str_replace('$B', "<br/>", $text);
$text = str_replace('$b', "<br/>", $text);
$skin->token("DETAILS", $text);

//objective section
$skin->flag("obj", !empty($d["quest_objective1"]));
for($i = 1; $i < 5; $i++)
{
	if(!empty($d["quest_objective".$i]))
	{
		$skin->addRow("obj", $d["quest_objective".$i]);
	}
}
$skin->flushRows("obj");

//collect item section
$skin->flag("collect", $d["quest_collectItem1ID"] > 0);
for($i = 1; $i < 5; $i++)
{
	if($d["quest_collectItem".$i."ID"] > 0)
	{
		$url = sprintf("item2.php?i=%s", $d["quest_collectItem".$i."ID"]);
		$item_name = !empty($d["c_name".$i]) ? $d["c_name".$i] : "Unknown Item";
		$skin->addRow("collect", $url, $item_name, $d["quest_collectItem".$i."Amount"]);
	}
}
$skin->flushRows("collect");

//kill creature section
$skin->flag("mob", $d["quest_killMob1ID"] > 0);
for($i = 1; $i < 5; $i++)
{
	if($d["quest_killMob".$i."ID"] > 0)
	{
		$url = sprintf("mob.php?i=%s", $d["quest_killMob".$i."ID"]);
		$mob_name = !empty($d["k_name".$i]) ? $d["k_name".$i] : "Unknown Creature";
		$skin->addRow("mob", $url, $mob_name, $d["quest_killMob".$i."Amount"]);
	}
}
$skin->flushRows("mob");

//given item section
$skin->flag("given", $d["quest_givenItem1ID"] > 0);
for($i = 1; $i < 5; $i++)
{
	if($d["quest_givenItem".$i."ID"] > 0)
	{
		$url = sprintf("item2.php?i=%s", $d["quest_givenItem".$i."ID"]);
		$item_name = !empty($d["gi_name".$i]) ? $d["gi_name".$i] : "Unknown Item";
		$skin->addRow("given", $url, $item_name, $d["quest_givenItem".$i."Amount"]);
	}
}
$skin->flushRows("given");

//choose item section
$skin->flag("choose", $d["quest_choiceItem1ID"] > 0);
for($i = 1; $i < 5; $i++)
{
	if($d["quest_choiceItem".$i."ID"] > 0)
	{
		$url = sprintf("item2.php?i=%s", $d["quest_choiceItem".$i."ID"]);
		$item_name = !empty($d["ci_name".$i]) ? $d["ci_name".$i] : "Unknown Item";
		$skin->addRow("choose", $url, $item_name, $d["quest_choiceItem".$i."Amount"]);
	}
}
$skin->flushRows("choose");

$reward = ($d["quest_givenItem1ID"] > 0 || $d["quest_choiceItem1ID"] > 0);
$skin->flag("reward", $reward);

$req = ($d["quest_killMob1ID"] > 0 || $d["quest_collectItem1ID"] > 0 || !empty($d["quest_objective1"]));
$skin->flag("require", $req);

$skin->dump();

$result->close();

?>

