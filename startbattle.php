
<?php
require "classes.php";
session_start();
//TODO check if active battle exists
    if(isset($_SESSION['battle'])){
        echo "oops it looks like you've already started a battle.";
        echo "<a href='fight.php'>Click here to resume</a>";
        return;
    }

    //set up the challengers and the opponents here
    //TODO make this a call to something
    $player = new Combatant("Kori", "draik_darigan", 1, 5, 5, 15, 10, true);
    $player->set_images("https://images.neopets.com/", true, "right");

	$opponent = new Combatant("Bad Guy1", "acara_blue", 1, 5, 5, 10, 6, false);
    $opponent->set_images("https://images.neopets.com/", true, "left");

    $battle = new Battle($player, $opponent);
    $_SESSION['battle'] = $battle;


    $battle->display_battle();
    //$displayhandler = new DisplayHandler($battle);
    //$_SESSION['displayhandler'] = $displayhandler;
    //$displayhandler->display_table($player, $p$opponent, $player->get_moves());

    ?>