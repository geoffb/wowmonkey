<?php

include "includes/common.php";

if(!empty($_REQUEST["i"]))
{
	$id = $_REQUEST["i"];
}
else
{
	die("No itemset specified!");
}

$sql = sprintf("call sp_getitemset(%s);", $id);

$db = DB_GetConnection();
if($db->multi_query($sql))
{
	$itemset = 	$db->store_result();
	if($db->next_result())
	{
		$items = $db->store_result();
	}
}
$db->close();


$i = $itemset->fetch_assoc();

$skin = new skin("itemset.skn");
$skin->token("SET_NAME", $i["itemset_name"]);

//init set stats
$setStrength	= 0;
$setAgility		= 0;
$setIntellect	= 0;
$setStamina		= 0;
$setSpirit		= 0;
$setArmor			= 0;

//init set resists
$setResHoly		=	0;
$setResArcane	= 0;
$setResFire		= 0;
$setResFrost	= 0;
$setResNature	= 0;
$setResShadow	= 0;

//items in set
$num_rows = $items->num_rows;
for($r = 0; $r < $num_rows; $r += 2)
{
	for($r2 = 0; $r2 < 2; $r2++)
	{
		if($d = $items->fetch_assoc())
		{
			$h[$r2] = buildItemBox($d);
			if(isset($d["item_armor"])){ $setArmor = $setArmor + $d["item_armor"];}
			if(isset($d["item_statStr"])){ $setStrength = $setStrength + $d["item_statStr"];}
			if(isset($d["item_statAgi"])){ $setAgility = $setAgility + $d["item_statAgi"];}
			if(isset($d["item_statSta"])){ $setStamina = $setStamina + $d["item_statSta"];}
			if(isset($d["item_statInt"])){ $setIntellect = $setIntellect + $d["item_statInt"];}
			if(isset($d["item_statSpr"])){ $setSpirit = $setSpirit + $d["item_statSpr"];}
			if(isset($d["item_resistHoly"])){ $setResHoly = $setResHoly + $d["item_resistHoly"];}
			if(isset($d["item_resistFire"])){ $setResFire = $setResFire + $d["item_resistFire"];}
			if(isset($d["item_resistNature"])){ $setResNature = $setResNature + $d["item_resistNature"];}
			if(isset($d["item_resistFrost"])){ $setResFrost = $setResFrost + $d["item_resistFrost"];}
			if(isset($d["item_resistShadow"])){ $setResShadow = $setResShadow + $d["item_resistShadow"];}
			if(isset($d["item_resistArcane"])){ $setResArcane = $setResArcane + $d["item_resistArcane"];}
		}
		else
		{
			$h[$r2] = "";
		}
	}
	$skin->addRow("items", $h[0], $h[1], $h[2]);
}
$skin->flushRows("items");

//set bonuses
for($b = 1; $b <= 8; $b++)
{
	if($i["itemset_spell".$b."ID"] > 0)
	{
		//store the keys since they're usually out of order
		$j[substr($i["itemset_bonus".$b],-1,1)] = $b;
	}
}
for($b = 1; $b <= 8; $b++)
{
	if(isset($j[$b]))
	{
		//and then display them in the correct order
		$skin->addRow("bonus", $i["itemset_bonus".$j[$b]], $i["spell".$j[$b]."text"], "spell.php?i=".$i["itemset_spell".$j[$b]."ID"]);
	}
}
$skin->flushRows("bonus");

//set stats
if($setArmor > 0){	$skin->addRow("stats","Armor",$setArmor);}
if($setStrength > 0){	$skin->addRow("stats","Strength",$setStrength);}
if($setAgility > 0){	$skin->addRow("stats","Agility",$setAgility);}
if($setStamina > 0){	$skin->addRow("stats","Stamina",$setStamina);}
if($setIntellect > 0){	$skin->addRow("stats","Intellect",$setIntellect);}
if($setSpirit > 0){	$skin->addRow("stats","Spirirt",$setSpirit);}
$skin->flushRows("stats");

//set resists
if($setResHoly > 0){	$skin->addRow("resists","Holy",$setResHoly);}
if($setResFire > 0){	$skin->addRow("resists","Fire",$setResFire);}
if($setResNature > 0){	$skin->addRow("resists","Nature",$setResNature);}
if($setResFrost > 0){	$skin->addRow("resists","Frost",$setResFrost);}
if($setResShadow > 0){	$skin->addRow("resists","Shadow",$setResShadow);}
if($setResArcane > 0){	$skin->addRow("resists","Arcane",$setResArcane);}
$skin->flushRows("resists");



//output
$skin->dump();

?>
