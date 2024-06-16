<?php
   class DisplayHandler {


    function __construct(){

    }

    function display_hp_bar($max_hp, $current_hp){
        $bg_color = "green";
            switch($hp_ratio = $current_hp/$max_hp){
                case ($hp_ratio <= 0):
                    $bg_color = "grey";
                    break;
                case($hp_ratio <= .25):
                    $bg_color = "red";
                    break;
                case($hp_ratio <= .50):
                    $bg_color = "orange";
                    break;
                default:
                    $bg_color= "limegreen";
                    break;
            }
            return("<td style='background:$bg_color'>" . $current_hp . " / " . $max_hp . "</td>\n");
      
    }
    //TODO make this dynamic
    function display_move_options($moveset){
        echo ("<td><select name='player_move'>
        <option value='closeattack'>Close Attack</option>
        <option value='rangedattack'>Ranged Attack</option>
        <option value='defended'>Defend</option>
        </select></td>
        ");
    }

    //TODO give this more flavor text options
    function narrate_attack($attacker, $dmg, $target){
        return "$attacker hit $target for $dmg HP!";
    }
    function narrate_win($winner){
    //TODO flavor text for winning
    echo("Congratulations to $winner!");
    }

    //display the player and the opponent
    function display_table($player, $player_current_hp, $opponent, $opponent_current_hp){
        
        echo ("<form action='fight.php' method='post'> \n");


        echo ("<table><tr>");
        echo ("<td><img src=" . $player->get_image() . "></td>");
        echo ("<td width=10%></td>");
        echo ("<td><img src=" . $opponent->get_image() . "></td>");
        echo ("\n</tr><tr>");
        echo ("<td>" . $player->get_name() . "</td>");
        echo ("<td width=10%></td>");
        echo ("<td>" . $opponent->get_name() . "</td>");
        echo ("\n</tr><tr>");
        echo ($this->display_hp_bar($player->get_max_hp(), $player_current_hp)); 
        echo ("<td width=10%></td>");
        echo ($this->display_hp_bar($opponent->get_max_hp(), $opponent_current_hp));

        echo ("</tr><tr>");
        $this->display_move_options($player->get_moves());
        echo ("</tr><tr>");

        echo ("</tr>\n</table> \n<input type='submit' value='Fight!' action='POST'/></form>");
    }
}

?>