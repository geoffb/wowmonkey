<?php

include_once "db.php";
include_once "lookup.php";
require_once("class.skin.php");

define("SITE_NAME", "WoW Monkey", true);
define("SITE_TAGLINE", "World of WarCraft Database", true);



////////////////////////////
// BEGIN COMMON FUNCTIONS //
////////////////////////////

function DB_GetItem($item_id)
{
	if( is_numeric($item_id) )
	{
		$sql = sprintf("select * from vw_item where item_id = %s", $item_id);
		$db = DB_GetConnection();
		return $db->query($sql);
		$db->close();
	}
}

function DB_GetSpell($spell_id)
{
	if( is_numeric($spell_id) )
	{
		$sql = sprintf("SELECT * FROM spell2 s left join spellicon i on i.spellicon_id = s.spell_iconid where spell_id = %s", $spell_id);
		$db = DB_GetConnection();
		return $db->query($sql);
		$db->close();
	}
}

function buildItemLink($d)
{
	return sprintf("<a class=\"q%s\" href=\"item2.php?i=%s\">%s</a>", $d["item_qualityID"], $d["item_id"], $d["item_name"]);
}

function buildShortSetLink($d)
{
		return sprintf("&nbsp;(<a class=\"quest\" href=\"itemset.php?i=%s\">set</a>&nbsp;)", $d["item_setID"]);
}

function buildSetLink($d)
{
		return sprintf("<a class=\"itemset\" href=\"itemset.php?i=%s\">%s</a>", $d["item_setID"], $d["itemset_name"] );
}

function getIconPath($icon)
{
	return "gfx/icons/" . $icon . ".PNG";
}

function buildItemBox($d)
{
	//fill skin file with item data
	$skin = new skin("item_box.skn");

	$skin->token("QUALITY_ID", $d["item_qualityID"]);
	$skin->token("NAME", $d["item_name"]);

	$skin->flag("bond", $d["item_bondID"] > 0);
	$skin->token("BOND_TYPE", Text_BondType($d["item_bondID"]));

	$skin->flag("unique", $d["item_isUnique"] == 1);

	$skin->flag("slot", $d["item_slotID"] > 0);
	$skin->token("SLOT_TYPE", Text_SlotType($d["item_slotID"]));

	$skin->token("SUB_CLASS", $d["itemsubclass_name"]);

	$skin->flag("armor", $d["item_armor"] > 0);
	$skin->token("ARMOR", $d["item_armor"]);

	$skin->flag("dmg1", $d["item_dmg1min"] > 0);
	if($d["item_dmg1min"] > 0)
	{
		$speed = $d["item_weaponDelay"] / 1000;
		$dps = (($d["item_dmg1min"] + $d["item_dmg1max"]) / 2) / $speed;
		$skin->token("DMG1_RANGE", sprintf("%s - %s", $d["item_dmg1min"], $d["item_dmg1max"]));
		$skin->token("WEAPON_SPEED", number_format($speed, 2));
		$skin->token("WEAPON_DPS", number_format($dps, 1));
	}

	if($d["item_statStr"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Strength", $d["item_statStr"]));
	}

	if($d["item_statAgi"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Agility", $d["item_statAgi"]));
	}

	if($d["item_statSta"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Stamina", $d["item_statSta"]));
	}

	if($d["item_statInt"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Intellect", $d["item_statInt"]));
	}

	if($d["item_statSpr"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Spirit", $d["item_statSpr"]));
	}

	if($d["item_resistHoly"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Holy Resistance", $d["item_statHoly"]));
	}

	if($d["item_resistFire"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Fire Resistance", $d["item_resistFire"]));
	}

	if($d["item_resistNature"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Nature Resistance", $d["item_resistNature"]));
	}

	if($d["item_resistFrost"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Frost Resistance", $d["item_resistFrost"]));
	}

	if($d["item_resistShadow"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Shadow Resistance", $d["item_resistShadow"]));
	}

	if($d["item_resistArcane"] > 0)
	{
		$skin->addRow("stats", sprintf("+%s Arcane Resistance", $d["item_resistArcane"]));
	}

	$skin->flushRows("stats");

	if($d["item_spell1ID"] > 0)
	{
		$skin->addRow("spells", Text_SpellTrigger($d["item_spell1TriggerID"]) . ": " . $d["spell1text"], "spell.php?i=".$d["item_spell1ID"]);
	}

	if($d["item_spell2ID"] > 0)
	{
		$skin->addRow("spells", Text_SpellTrigger($d["item_spell2TriggerID"]) . ": " . $d["spell2text"], "spell.php?i=".$d["item_spell2ID"]);
	}

	if($d["item_spell3ID"] > 0)
	{
		$skin->addRow("spells", Text_SpellTrigger($d["item_spell3TriggerID"]) . ": " . $d["spell3text"], "spell.php?i=".$d["item_spell3ID"]);
	}

	$skin->flushRows("spells");

	$skin->flag("reqlvl", $d["item_reqLevel"] > 1);
	$skin->token("REQ_LEVEL", $d["item_reqLevel"]);

	$skin->flag("quest", $d["item_beginQuestID"] > 0);
	$skin->token("QUEST_URL", sprintf("quest.php?i=%s", $d["item_beginQuestID"]));

	$skin->flag("descrip", $d["item_description"] != null);
	$skin->token("DESCRIPTION", "\"" . $d["item_description"] . "\"");

	$skin->flag("set", $d["item_setID"] > 0);
	$skin->token("SET_NAME", $d["itemset_name"]);
	$skin->token("ITEMSET_URL", sprintf("itemset.php?i=%s", $d["item_setID"]));

	return $skin->html;
}

function doErrMsg($msg)
{
	return "<div class=\"err\">$msg</div>";
}

function doOkMsg($msg)
{
	return "<div class=\"ok\">$msg</div>";
}

function save_item($d)
{

	global $database;

	$stats = array(0,0,0,0,0,0,0,0,0,0);
	$stats[$d[29]] = $d[30];
	$stats[$d[31]] = $d[32];
	$stats[$d[33]] = $d[34];
	$stats[$d[35]] = $d[36];
	$stats[$d[37]] = $d[38];
	$stats[$d[39]] = $d[40];
	$stats[$d[41]] = $d[42];
	$stats[$d[43]] = $d[44];
	$stats[$d[45]] = $d[46];
	$stats[$d[47]] = $d[48];

	$q = "call sp_saveitem(";
	$q .= $d[1].","; //item id
	$q .= $d[3].","; //class id
	$q .= $d[4].","; //subclass id
	$q .= prep_str($d[5]).","; //name
	$q .= $d[9].",";  //icon id
	$q .= $d[10].","; //quality id
	$q .= $d[14].","; //slot id
	$q .= $d[17].","; //item level
	$q .= $d[18].","; //req'd level
	$q .= $d[26].","; //is unique
	$q .= $stats[3].","; //agility
	$q .= $stats[4].","; //strength
	$q .= $stats[7].","; //stamina
	$q .= $stats[5].","; //intelligence
	$q .= $stats[6].","; //spirit
	$q .= $d[64].","; //armor
	$q .= $d[65].","; //resist holy
	$q .= $d[66].","; //resist fire
	$q .= $d[67].","; //resist nature
	$q .= $d[68].","; //resist frost
	$q .= $d[69].","; //resist shadow
	$q .= $d[70].","; //resist arcane
	$q .= $d[71].","; //weapon delay
	$q .= $d[72].","; //ammo type
	$q .= $d[104].","; //bond id
	$q .= $d[111].","; //materiel id
	$q .= $d[114].","; //block value
	$q .= $d[115].","; //set id
	$q .= $d[116].","; //durability
	$q .= prep_str($d[105]).","; //description
	$q .= $d[74].","; //spell id 1
	$q .= $d[75].","; //spell trigger 1
	$q .= $d[80].","; //spell id 2
	$q .= $d[81].","; //spell trigger 2
	$q .= $d[86].","; //spell id 3
	$q .= $d[87].","; //spell trigger 3
	$q .= $d[49].","; //damage min 1
	$q .= $d[50].","; //damage max 1
	$q .= $d[109].")"; //begin quest id

	if(!isset($database))
	{
		$database = DB_GetConnection();
	}

	if($result = $database->multi_query($q))
			{
				do
				{
			       	/* store first result set */
			       	if ($result = $database->store_result())
			    	{
			   			$result->close();
					}
				} while ($database->next_result());
			}
			//printf($database->error . "<hr/>");

}

function save_quest($d)
{

}

function save_mob($d)
{

}

function XlateClassBinFlag($flag)
{
	$text = "";
	for($x = 1; $x < 10; $x++)
	{
		if($flag & $x)
		{
			$text .= Text_Class($x).", ";
		}
	}
	if(strlen($text) > 0)
	{
		$text = "Classes: ".substr($text, 0, -2);
	}
}

?>

