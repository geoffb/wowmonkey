<?php

//search.php

include "includes/common.php";

$box = new skin("search_form.skn");

if(!empty($_REQUEST["s"]))
	$s = $_REQUEST["s"];

if(!empty($_REQUEST["t"]))
{
	$t = $_REQUEST["t"];
}
else
{
	$t = 0; //Everything
}

if(!empty($s))
	$strtmp = str_replace(" ", "", $s);

if(!empty($strtmp))
{
	$db = DB_GetConnection();

	if($t == 0 || $t == 1)
	{
		$sql = "select * from vw_item where item_name like '%" . $s . "%' order by item_name";
		$items = $db->query($sql);
	}

	if($t == 0 || $t == 2)
	{
		$sql = "select * from quest where quest_name like '%".$s."%' order by quest_name";
		$quests = $db->query($sql);
	}

	if($t == 0 || $t == 3)
	{
		$sql = "select * from spell2 where spell_name like '%".$s."%' order by spell_name";
		$spells = $db->query($sql);
	}

	$db->close();

	$skin = new skin("search_result.skn");

	$box->token("SEARCH_VALUE", $s);
	$skin->token("SEARCH_BOX", $box->html);

	$skin->token("NUM_ITEMS", $items->num_rows);
	$skin->token("NUM_QUESTS", $quests->num_rows);
	$skin->token("NUM_SPELLS", $spells->num_rows);

	for($r = 0; $r < $items->num_rows; $r++)
	{
		$d = $items->fetch_assoc();
		$icon = getIconPath($d["itemdisplay_icon"]);
		if($d["item_setID"] > 0)
		{
			$skin->addRow("items", buildItemLink($d), $d["item_reqLevel"], $icon, "<br/>".buildSetLink($d));
		}
		else
		{
			$skin->addRow("items", buildItemLink($d), $d["item_reqLevel"], $icon, "");
		}

	}
	$skin->flushRows("items");

	for($r = 0; $r < $quests->num_rows; $r++)
	{
		$d = $quests->fetch_assoc();
		$url = sprintf("quest.php?i=%s", $d["quest_id"]);
		$skin->addRow("quests", $d["quest_name"], $url);
	}
	$skin->flushRows("quests");

	for($r = 0; $r < $spells->num_rows; $r++)
	{
		$d = $spells->fetch_assoc();
		$url = sprintf("spell.php?i=%s", $d["spell_id"]);
		$skin->addRow("spells", $d["spell_name"], $url);
	}
	$skin->flushRows("spells");

	$skin->dump();
}
else
{
	$box->token("SEARCH_VALUE", "");
	$box->dump();
}

?>
