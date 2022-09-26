<?php
require "classes.php";
session_start();
$battleparty = $_SESSION["battleparty"];
$opponentparty = $_SESSION["opponentparty"];


//home team goes first
for ($i = 0;  $i <4; $i++){
    if($battleparty->petArray[$i]->current_hp >0){
        $attack_type = $_POST["pet" . $i . "_attack"];
        $attack_power = $battleparty->petArray[$i]->deal_damage($attack_type);
        $target = $_POST["pet" . $i . "_target"];
        //echo ("pet" . $i . " deals damage to ". $target);
        $opponentparty->petArray[$target]->take_damage($attack_power);
    }
}

//now for the opponents
foreach ($opponentparty->petArray as $opponent){
    if ($opponent->current_hp > 0){
        $targets = $battleparty->get_valid_targets;
        //TODO actually have them do anything lol
    }

}


if($battleparty->check_loss()){
    echo "You lost! Sorry :(";
}

if($opponentparty->check_loss()){
    echo "You win!";
}


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

$_SESSION["battleparty"] = $battleparty;
$_SESSION["opponentparty"] = $opponentparty;

echo ("</tr>\n</table> \n<input type='submit' value='Fight!' action='POST'/></form>");

?>