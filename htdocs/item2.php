<?php

include "includes/common.php";
//include "includes/forum.php";

if(!empty($_REQUEST["i"]))
{
	$id = $_REQUEST["i"];
}
else
{
	die("No item specified!");
}

//get item data
$item = DB_GetItem($id);
$d = $item->fetch_assoc();

/*
$thread_id = $d["item_thread_id"];
if($thread_id == 0)
{
	$thread_id = saveThread(0, "User Comments");
	$sql = sprintf("UPDATE item SET item_thread_id = %s WHERE item_id = %s", $thread_id, $id);
	$db = DB_GetConnection();
	$db->query($sql);
	$db->close();
}
*/

$item = buildItemBox($d);

$skin = new skin("item2.skn");

$skin->token("ITEM_NAME", $d["item_name"]);
$skin->token("ITEM_BOX", $item);

$skin->token("ICON_SRC", getIconPath($d["itemdisplay_icon"]));

$skin->addRow("props", "Item ID", $id);
$skin->addRow("props", "Item Level", $d["item_level"]);
$skin->flushRows("props");

//User comments section
//$skin->token("USER_COMMENTS", getThreadHtml($thread_id));
//$skin->token("ADD_COMMENT", getQuickReplyHtml($thread_id));

$skin->dump();

?>
