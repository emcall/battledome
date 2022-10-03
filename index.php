<?php
	session_start();

	require "classes.php";

//This would be a pull from the database/session data

	$pet1 = new Combatant(0, "Kori", "draik_darigan", 1, 3, 3, 10);
	$pet2 = new Combatant(1, "Varien", "krawk_royalgirl", 1, 3, 3, 10);
	$pet3 = new Combatant(2, "Luikar", "draik_brown", 1, 3, 3, 10);
	$pet4 = new Combatant(3, "Cazarus", "draik_mutant", 1, 3, 3, 10);

	$opponent1 = new Combatant(0, "Bad Guy1", "acara_blue", 1, 3, 3, 10);
	$opponent2 = new Combatant(1, "Bad Guy2", "aisha_blue", 1, 3, 3, 10);


	$battleparty = new BattleParty([$pet1, $pet2, $pet3, $pet4]);
	$opponentparty = new BattleParty([$opponent1, $opponent2]);
	$displayhandler = new DisplayHandler();

$_SESSION["battleparty"] = $battleparty;
$_SESSION["opponentparty"] = $opponentparty;
$_SESSION["displayhandler"] = $displayhandler;

$displayhandler->display_table($battleparty, $opponentparty);

?>