<?php

//Slot Type
define("SLOT_NONE", 0);
define("SLOT_HEAD", 1);
define("SLOT_NECK", 2);
define("SLOT_SHOULDERS", 3);
define("SLOT_SHIRT", 4);
define("SLOT_VEST", 5);
define("SLOT_WAIST", 6);
define("SLOT_LEGS", 7);
define("SLOT_FEET", 8);
define("SLOT_WRIST", 9);
define("SLOT_HANDS", 10);
define("SLOT_RING", 11);
define("SLOT_TRINKET", 12);
define("SLOT_1HAND", 13);
define("SLOT_SHIELD", 14);
define("SLOT_BOW", 15);
define("SLOT_BACK", 16);
define("SLOT_2HAND", 17);
define("SLOT_BAG", 18);
define("SLOT_TABBARD", 19);
define("SLOT_ROBE", 20);
define("SLOT_MAINHAND", 21);
define("SLOT_OFFHAND", 22);
define("SLOT_HELD", 23);
define("SLOT_AMMO", 24);
define("SLOT_THROWN", 25);
define("SLOT_RANGED", 26);

//Bond Type
define("BOND_PICKUP", 1);
define("BOND_EQUIP", 2);
define("BOND_USE", 3);
define("BOND_QUEST1", 4);
define("BOND_QUEST2", 5);

//Spell Trigger
define("SPELL_TRIGGER_USE", 0);
define("SPELL_TRIGGER_EQUIP", 1);
define("SPELL_TRIGGER_ONHIT", 2);

function Text_SlotType($index)
{
	switch($index)
	{
	case SLOT_NONE:
		return "None";
		break;
	case SLOT_HEAD:
		return "Head";
		break;
	case SLOT_NECK:
		return "Neck";
		break;
	case SLOT_SHOULDERS:
		return "Shoulders";
		break;
	case SLOT_SHIRT:
		return "Shirt";
		break;
	case SLOT_VEST:
		return "Vest";
		break;
	case SLOT_WAIST:
		return "Waist";
		break;
	case SLOT_LEGS:
		return "Legs";
		break;
	case SLOT_FEET:
		return "Feet";
		break;
	case SLOT_WRIST:
		return "Wrist";
		break;
	case SLOT_HANDS:
		return "Hands";
		break;
	case SLOT_RING:
		return "Finger";
		break;
	case SLOT_TRINKET:
		return "Trinket";
		break;
	case SLOT_1HAND:
		return "One Hand";
		break;
	case SLOT_SHIELD:
		return "Shield";
		break;
	case SLOT_BOW:
		return "Bow";
		break;
	case SLOT_BACK:
		return "Back";
		break;
	case SLOT_2HAND:
		return "Two Hand";
		break;
	case SLOT_BAG:
		return "Bag";
		break;
	case SLOT_TABBARD:
		return "Tabbard";
		break;
	case SLOT_ROBE:
		return "Robe";
		break;
	case SLOT_MAINHAND:
		return "Main Hand";
		break;
	case SLOT_OFFHAND:
		return "Off Hand";
		break;
	case SLOT_HELD:
		return "Held In Off-hand";
		break;
	case SLOT_AMMO:
		return "Ammo";
		break;
	case SLOT_THROWN:
		return "Thrown";
		break;
	case SLOT_RANGED:
		return "Ranged";
		break;
	}
}

function Text_BondType($index)
{
	switch($index)
	{
	case BOND_PICKUP:
		return "Binds when picked up";
		break;
	case BOND_EQUIP:
		return "Binds when equipped";
		break;
	case BOND_USE:
		return "Binds on use";
		break;
	case BOND_QUEST1:
		return "Quest Item";
		break;
	case BOND_QUEST2:
		return "Quest Item";
		break;
	}
}

function Text_SpellTrigger($index)
{
	switch($index)
	{
	case SPELL_TRIGGER_USE:
		return "Use";
		break;
	case SPELL_TRIGGER_EQUIP:
		return "Equip";
		break;
	case SPELL_TRIGGER_ONHIT:
		return "Chance On Hit";
		break;
	}
}

?>