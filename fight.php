<?php
require "classes.php";
session_start();
 //TODO this commented out bit is because battle and opponent party will be created on the fight page
if (!isset($_SESSION["battleround"])){
    $_SESSION["battleround"] = 0;
    //process the challengers and the opponents here
    //TODO make this a call to something
    $pet1 = new Combatant(0, "Kori", "draik_darigan", 1, 3, 3, 10);
	$pet2 = new Combatant(1, "Varien", "krawk_royalgirl", 1, 3, 3, 10);
	$pet3 = new Combatant(2, "Luikar", "draik_brown", 1, 3, 3, 10);
	$pet4 = new Combatant(3, "Cazarus", "draik_mutant", 1, 3, 3, 10);

	$opponent1 = new Combatant(0, "Bad Guy1", "acara_blue", 1, 1, 1, 10);
	$opponent2 = new Combatant(1, "Bad Guy2", "aisha_blue", 1, 1, 1, 10);


	$battleparty = new BattleParty([$pet1, $pet2, $pet3, $pet4]);
	$opponentparty = new BattleParty([$opponent1, $opponent2]);
}
else{
    $_SESSION["battleround"]++; 
    $battleparty = $_SESSION["battleparty"];
    $opponentparty = $_SESSION["opponentparty"];
}

//home team goes first

//TODO this doesn't take into account defense boosts from opponents attack choice
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
        //first pick a target
        $targets = $battleparty->get_valid_targets();
        $chosentarget = rand(0, (count($targets)-1));
        //then pick an attack
        $moves = $opponent->get_moves();
        $chosenmove = $moves[rand(0, count($moves)-1)];
        $attack_power = $opponent->deal_damage($chosenmove);
        $battleparty->petArray[$chosentarget]->take_damage($attack_power);
    }

}

echo ("ROUND " .  $_SESSION['battleround']);


if($battleparty->check_loss()){
    echo "You lost! Sorry :(";
    unset($_SESSION["battleround"]);
}

elseif($opponentparty->check_loss()){
    echo "You win!";
    
    unset($_SESSION["battleround"]);
}
else{

    $_SESSION["battleparty"] = $battleparty;
    $_SESSION["opponentparty"] = $opponentparty;

    $displayhandler = $_SESSION["displayhandler"];

    $displayhandler->display_table($battleparty, $opponentparty);

}
?>