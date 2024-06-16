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
if(!isset($_POST["player_move"])){
   $battle->display_battle();
}
else{
    $battle->process_round($_POST["player_move"]);
}

?>