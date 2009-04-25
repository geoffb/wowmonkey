<?php

//search.php

include "includes/common.php";

if(!empty($_REQUEST["c"]))
	$searchCategory = $_REQUEST["c"];
else
	$searchCategory = 0;

switch ($searchCategory)
{

case "1":
	$box = new skin("adv_item_search_form.skn");
	//$box->setPageTitle("Advanced Item Search");
	break;

case "2":
	$box = new skin("adv_quest_search_form.skn");
	//$box->setPageTitle("Advanced Quest Search");
	break;

case "3":
	$box = new skin("adv_creature_search_form.skn");
	//$box->setPageTitle("Advanced Creature Search");
	break;

default:
	$box = new skin("adv_search_form.skn");
	//$box->setPageTitle("Advanced Search");

} //switch ($searchCategory)

if(!empty($_REQUEST["s"]))
	$s = $_REQUEST["s"];

if(!empty($s))
	$strtmp = str_replace(" ", "", $s);

if(!empty($strtmp))
{
	$db = DB_GetConnection();
	$sql = "select * from vw_item where item_name like '%" . $s . "%' order by item_name";
	$items = $db->query($sql);
	$sql = "select * from quest where quest_name like '%".$s."%' order by quest_name";
	$quests = $db->query($sql);
	$db->close();

	$skin = new skin("search_result.skn");

	$box->token("SEARCH_VALUE", $s);
	$skin->token("SEARCH_BOX", $box->html);

	$skin->token("NUM_ITEMS", $items->num_rows);
	$skin->token("NUM_QUESTS", $quests->num_rows);

	for($r = 0; $r < $items->num_rows; $r++)
	{
		$d = $items->fetch_assoc();
		$icon = getIconPath($d["itemdisplay_icon"]);
		$skin->addRow("items", buildItemLink($d), $d["item_reqLevel"], $icon);
	}
	$skin->flushRows("items");

	for($r = 0; $r < $quests->num_rows; $r++)
	{
		$d = $quests->fetch_assoc();
		$url = sprintf("quest.php?i=%s", $d["quest_id"]);
		$skin->addRow("quests", $d["quest_name"], $url);
	}
	$skin->flushRows("quests");

	$skin->dump();
}
else
{
	$box->token("SEARCH_VALUE", "");

	$skin = new skin("adv_search.skn");
	$skin->token("STUFF", $box->html);

	$skin->dump();
}

?>
