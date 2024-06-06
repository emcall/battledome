<?php
require "classes.php";
session_start();

//set up a battle if there isn't one. 
//this will be moved onto its own page (startbattle or something like that)
if (!isset($_SESSION["battleround"])){
    $_SESSION["battleround"] = 0;
    //set up the challengers and the opponents here
    //TODO make this a call to something
    $player = new Combatant(0, "Kori", "draik_darigan", 1, 3, 3, 10, 1);

	$opponent = new Combatant(0, "Bad Guy1", "acara_blue", 1, 1, 1, 10, 1);
    $opponent->set_images("https://images.neopets.com/", true, "right");

    $displayhandler = new DisplayHandler();
}
else{
    $_SESSION["battleround"]++; 
    $player = $_SESSION["player"];
    $opponent = $_SESSION["opponent"];
    $displayhandler = $_SESSION["displayhandler"];

}

//Deal damage for the player
//TODO take speed into account, right now player goes first
//TODO take defense buffs into account
    $attack_type = $_POST["player_attack"];
    $attack_power = $player->calculate_attack_power($attack_type);
    //TODO this should return the amount of damage it does
    $opponent->take_damage($attack_power);
    echo ("Player deals damage!");







//now for the opponents
/*foreach ($opponentparty->petArray as $opponent){
    if ($opponent->current_hp > 0){
        //first pick a target
        $targets = $battleparty->get_valid_targets();
        $chosentarget = rand(0, (count($targets)-1));
        //then pick an attack
        $moves = $opponent->get_moves();
        $chosenmove = $moves[rand(0, count($moves)-1)];
        $attack_power = $opponent->calculate_attack_power($chosenmove);
        $battleparty->petArray[$chosentarget]->take_damage($attack_power);
    }

}
*/
echo ("ROUND " .  $_SESSION['battleround']);


if($player->check_loss()){
    echo "You lost! Sorry :(";
    echo "<a href=/battledome> Back to the Battledome Homepage</a>";
    unset($_SESSION["battleround"]);
}

elseif($opponent->check_loss()){
    echo "You win!";
    echo "<a href=/battledome> Back to the Battledome Homepage</a>";
    unset($_SESSION["battleround"]);
    unset($_SESSION["battleparty"]);
    unset($_SESSION["opponentparty"]);

}
else{

    $_SESSION["player"] = $player;
    $_SESSION["opponent"] = $opponent;

    $displayhandler = $_SESSION["displayhandler"];

    $displayhandler->display_table($player, $opponent);

}
?>