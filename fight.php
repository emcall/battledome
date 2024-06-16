<?php
require "classes.php";
session_start();

//check if there is presently a battle running
if (!isset($_SESSION["battle"])){
echo "You are not currently in a battle! Would you like to start one?";
//TODO make start battle
return;
}
$battle = $_SESSION["battle"];
$player_move_type = $_POST["player_attack"];

$battle->process_round($player_move_type);
$displayhandler = $_SESSION['displayhandler'];

?>