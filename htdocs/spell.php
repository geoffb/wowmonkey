<?php

include "includes/common.php";

if(!empty($_REQUEST["i"]))
{
	$id = $_REQUEST["i"];
}
else
{
	die("No spell specified!");
}

//get item data
$spell = DB_GetSpell($id);
$d = $spell->fetch_assoc();

$skin = new skin("spell.skn");

$skin->token("SPELL_NAME", $d["spell_name"]);
$skin->token("FINAL_TEXT", $d["spell_final_text"]);

$p = strrpos($d["spellicon_icon"], "\\");
$icon = substr($d["spellicon_icon"], $p + 1);
$skin->token("ICON_SRC", getIconPath($icon));

$skin->addRow("props", "Mana Cost", $d["spell_manacost"]);
$skin->addRow("props", "Max Stack", $d["spell_stackamount"]);

$skin->flushRows("props");

$skin->dump();

?>
