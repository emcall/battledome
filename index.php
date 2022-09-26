<?php
	//These numbers will definitely need to be altered to balance gameplay, hence why they're up here

	session_start();

	require "classes.php";

//This would be a pull from the database/session data

	$pet1 = new Combatant(0, "Kori", "draik_darigan", 1, 3, 3, 10);
	$pet2 = new Combatant(1, "Varien", "krawk_royalgirl", 1, 3, 3, 10);
	$pet3 = new Combatant(2, "Luikar", "draik_brown", 1, 3, 3, 10);
	$pet4 = new Combatant(3, "Cazarus", "draik_mutant", 1, 3, 3, 10);

	$opponent1 = new Opponent(0, "Bad Guy1", "acara_blue", 1, 1, 1, 100);
	$opponent2 = new Opponent(1, "Bad Guy2", "aisha_blue", 1, 1, 1, 100);


	$battleparty = new BattleParty([$pet1, $pet2, $pet3, $pet4]);
	$opponentparty = new BattleParty([$opponent1, $opponent2]);

$_SESSION["battleparty"] = $battleparty;
$_SESSION["opponentparty"] = $opponentparty;

//TODO display stuff will all go in its own nice little class, split out from the combatant/party classes. 
// this is easier for quick testing, i usually do display classes last

echo ("<form action='fight.php' method='post'> \n");


echo ("<table><tr>");
$battleparty->display_images();
echo ("<td width=10%></td>");
$opponentparty->display_images();
echo ("\n</tr><tr>");
$battleparty->display_names();
echo ("<td width=10%></td>");
$opponentparty->display_names();
echo ("\n</tr><tr>");
$battleparty->display_hp(); 
echo ("<td width=10%></td>");
$opponentparty->display_hp();

echo ("</tr><tr>");
$battleparty->display_attacks();
echo ("</tr><tr>");

$targets = $opponentparty->get_valid_targets();
$battleparty->display_targets($targets);


echo ("</tr>\n</table> \n<input type='submit' value='Fight!' action='POST'/></form>");
?>