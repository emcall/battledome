<?php
	session_start();

	require "classes.php";

//This would be a pull from the database/session data

	$player = new Combatant(0, "Kori", "draik_darigan", 1, 3, 3, 10, 1);

	$opponent = new Combatant(0, "Bad Guy", "acara_blue", 1, 3, 3, 10, 1);

	$displayhandler = new DisplayHandler();

$_SESSION["player"] = $player;
$_SESSION["opponent"] = $opponent;
$_SESSION["displayhandler"] = $displayhandler;

$displayhandler->display_table($player, $opponent);

?>