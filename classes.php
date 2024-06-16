<?php

//now ideally moves would have their own class to handle dynamic stuff like additional move types (heals etc) and differnet opponents having different abilities but that is a TODO for later

    //handles frontend display
    class DisplayHandler {
        public $battle = null;
        public $player = null;
        public $opponent = null;

        function __construct($battle){
            $this->battle = $battle;
            $this->player = $battle->player;
            $this->opponent = $battle->opponent;
        }

        function display_hp_bar($isPlayer){
            $bg_color = "green";
            if($isPlayer){
                switch($hp_ratio = $this->player->current_hp/$this->player->max_hp){
                    case ($hp_ratio <= 0):
                        $bg_color = "grey";
                        break;
                    case($hp_ratio <= 25):
                        $bg_color = "red";
                        break;
                    case($hp_ratio <= 50):
                        $bg_color = "orange";
                        break;
                    default:
                        $bg_color= "limegreen";
                        break;
                }
                return("<td style='background:$bg_color'>" . $this->player->current_hp . " / " . $this->player->max_hp . "</td>\n");
            }
            switch($hp_ratio = $this->opponent->current_hp/$this->opponent->max_hp){
                case ($hp_ratio <= 0):
                    $bg_color = "grey";
                    break;
                case($hp_ratio <= 25):
                    $bg_color = "red";
                    break;
                case($hp_ratio <= 50):
                    $bg_color = "orange";
                    break;
                default:
                    $bg_color= "limegreen";
                    break;
            }
            return("<td style='background:$bg_color'>" . $this->opponent->current_hp . " / " . $this->opponent->max_hp . "</td>\n");
    
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
        function display_table(){
                            
            echo ("<form action='fight.php' method='post'> \n");


            echo ("<table><tr>");
            echo ("<td><img src=" . $this->player->get_image() . "></td>");
            echo ("<td width=10%></td>");
            echo ("<td><img src=" . $this->opponent->get_image() . "></td>");
            echo ("\n</tr><tr>");
            echo ("<td>" . $this->player->get_name() . "</td>");
            echo ("<td width=10%></td>");
            echo ("<td>" . $this->opponent->get_name() . "</td>");
            echo ("\n</tr><tr>");
            echo ($this->display_hp_bar(true)); 
            echo ("<td width=10%></td>");
            echo ($this->display_hp_bar(false));

            echo ("</tr><tr>");
            $this->player->display_attacks();
            echo ("</tr><tr>");

            echo ("</tr>\n</table> \n<input type='submit' value='Fight!' action='POST'/></form>");
        }
    }


        //class for each fighter in the battledome (player and opponent)
    class Combatant {
        public $name = "name";
        public $species_color = "acara_blue";

        public $level = 1;
        public $attack = 1;
        public $defense = 1;
        public $max_hp = 1;
        public $speed = 1;
        
        public $current_hp = 0;
        public $current_attack_type = 'closeattack';

        public $close_attack_img;
        public $ranged_attack_img;
        public $defended_img;
        public $downed_img;
        public $isPlayer;
        
        function __construct($name, $species_color, $level, $attack, $defense, $max_hp, $speed, $isPlayer){
            $this->name = $name;
            $this->species_color = $species_color;
            $this->level = $level;
            $this->attack = $attack;
            $this->defense = $defense;
            $this->max_hp = $max_hp;
            $this->current_hp = $max_hp;
            $this->speed = $speed;
            
            if($isPlayer)
                $direction = "right";
            else
                $direction = "left";

            

            $this->close_attack_img = "https://images.neopets.com/pets/closeattack/" . $this->species_color . "_" . $direction . ".gif";
            $this->ranged_attack_img = "https://images.neopets.com/pets/rangedattack/" . $this->species_color . "_" . $direction . ".gif";
            $this->defended_img = "https://images.neopets.com/pets/defended/" . $this->species_color . "_" . $direction . ".gif";
            $this->downed_img = "https://images.neopets.com/pets/hit/" . $this->species_color . "_" . $direction . ".gif";
            //TODO handle equipment/weapons. Figure they just increase atk/def
        }


        //This is future-proofing if we want to add more types of moves later on
        function get_moves(){
            
            return (["defended", "closeattack", "rangedattack"]);
        }

        function get_name(){
            return $this->name;
        }

        function get_stats(){
            return ['level' => $this->level, 'attack' =>$this->attack, 'defense' => $this->defense, 'speed' => $this->speed];
        }
        
        function get_max_hp(){
            return $this->max_hp;
        }

        function get_current_hp(){
            return $this->current_hp;
        }
        function get_image(){
            //If we ever add more kinds of attacks this needs updating
            if ($this->current_hp <= 0)
                return $this->downed_img;
            switch ($this->current_attack_type){
                case "closeattack":
                    return $this->close_attack_img;
                case "rangedattack":
                    return $this->ranged_attack_img;
                case "defended":
                    return $this->defended_img;
                default:
                    return $this->close_attack_img;
            }
        }
        function set_current_move_type($move){
            //TODO: should check against get_moves to make sure atk is a valid type
            //if this->get_moves !contains atk throw an error
            $this->current_attack_type = $move;
        }
        
        //The set_images class handles where the images for the combatant come from. Should be run immediately after instantiation.
        function set_images($host, $is_pet, $direction, $close_attack_img = null, $ranged_attack_img = null, $defended_img = null, $downed_img = null){
            //"host" is the full host name such as https://leopets.net/images
            //"is_pet" is a boolean which means if it's using pet images (like if it's a blue acara) or something else. 
            //if something else, then full image URLs need to be provided
                if(!$is_pet){
                    $this->close_attack_img = $close_attack_img;
                    $this->ranged_attack_img = $ranged_attack_img;
                    $this->defended_img = $defended_img;
                    $this->downed_img = $downed_img; 
                    }
                else{
                    $this->close_attack_img = $host . "/pets/closeattack/" . $this->species_color . "_$direction.gif";
                    $this->ranged_attack_img = $host . "/pets/rangedattack/" . $this->species_color . "_$direction.gif";
                    $this->defended_img = $host .  "/pets/defended/" . $this->species_color . "_$direction.gif";
                    $this->downed_img = $host . "/pets/hit/" . $this->species_color . "_$direction.gif";
                }

            }

        
    


            
        

        function display_hp(){
            if ($this->current_hp <= 0)
                return("<td style='background:grey'>" . $this->current_hp . " / " . $this->max_hp . "</td> \n");
            if ($this->current_hp/$this->max_hp <= .25)
                return("<td style='background:red'>" . $this->current_hp . " / " . $this->max_hp . "</td>\n");
            if ($this->current_hp/$this->max_hp <= .50)
                return("<td style='background:orange'>" . $this->current_hp . " / " . $this->max_hp . "</td>\n");
            return("<td style='background:limegreen'>" . $this->current_hp . " / " . $this->max_hp . "</td>\n");
        }

        //TODO this should be a part of display handler...
        function display_attacks(){
            echo ("<td><select name='player_attack'>
                <option value='closeattack'>Close Attack</option>
                <option value='rangedattack'>Ranged Attack</option>
                <option value='defended'>Defend</option>
                </select></td>
                ");

        }
    }
  
        //handles the actual battle from backend
    class Battle {
        
        public $battleround = 0;
        public $isOver = false;

        public $player = null;
        public $player_stats = ['level' => 1, 'attack' => 1, 'defense' => 1, 'speed' => 1];
        public $player_current_hp = 1;
        public $player_max_hp = 1;
        public $player_moves = ["closeattack", "rangedattack", "defended"];

        public $opponent = null;
        public $opponent_stats = ['level' => 1, 'attack' => 1, 'defense' => 1, 'speed' => 1];
        public $opponent_current_hp = 1;
        public $opponent_max_hp = 1;
        public $opponent_moves = ["closeattack", "rangedattack", "defended"];

        public $display_handler = null;

        function __construct($player, $opponent)
        {
            $this->player = $player;
            $this->opponent = $opponent;

            $this->player_stats = $player->get_stats();
            $this->player_current_hp = $player->get_current_hp();
            $this->player_max_hp = $player->get_max_hp();
            $this->player_moves = $player->get_moves();
            
            $this->opponent_stats = $opponent->get_stats();
            $this->opponent_current_hp = $opponent->get_current_hp();
            $this->opponent_max_hp = $opponent->get_max_hp();
            $this->opponent_moves = $opponent->get_moves();
            $this->display_handler = $_SESSION["displayhandler"];

        }

        function calculate_damage($isPlayer, $move_type, $defense_boost){
                //pull up stats do a calculation then determine the amount of hp to be lost

                //TODO don't make this hard coded this should be dynamic
                $move_power = 0;
                switch($move_type){
                    case "closeattack":
                        $move_power = 40;

                        break;
                    case "rangedattack":
                        $move_power = 20;
                        break;
                    case "defended":
                        $move_power = 0;
                        break;
                    }         
                //current damage calculation is pokemon's minus irrelevant stuff like crits, types, etc
                if($isPlayer){
                    $damage = ((2 * $this->player_stats['level'])/5 * $move_power * ($this->player_stats['attack']/($this->opponent_stats['defense']*$defense_boost)))/50;  
                }
                else{
                    $damage = ((2 * $this->opponent_stats['level'])/5 * $move_power * ($this->opponent_stats['attack']/($this->player_stats['defense']*$defense_boost)))/50;
                }
                
                //TODO add randomness

                return ($damage);
        }

        function check_loss($isPlayer){
            if ($isPlayer){
                if($this->player_current_hp < 1)
                    return true;
                return false;
            }
            else
                if($this->opponent_current_hp < 1)
                        return true;
                    return false;
        }

        function end_battle($isPlayer){
            if($isPlayer){
                echo "You lost :(";
                echo "<a href=/battledome> Back to the Battledome Homepage</a>";
                unset($_SESSION["battle"]);
            }
            else{
                echo "You win!";
                echo "<a href=/battledome> Back to the Battledome Homepage</a>";
                unset($_SESSION["battle"]);
            }

        }
        function process_round($move_type){
            $this->battleround++;
            //determine what move the opponent chooses
            //TODO make this random
            $opponent_move = "rangedattack";
            //TODO get the defense boost for the chosen moves
            $player_defense_boost = '1';
            $opponent_defense_boost = '2';

            //determine who is moving first
            //TODO this is very wet code dry it out
            switch ($x = $this->player_stats['speed'] - $this->opponent_stats['speed']){
                case ($x >0): //player is faster
                        //Player damages opponent
                        $opponent_damage = $this->calculate_damage(true, $move_type, $opponent_defense_boost);
                        echo("Player did $opponent_damage to opponent!");
                        //deal damage to opponent
                        $this->opponent_current_hp -= $opponent_damage;
                        echo($this->opponent_current_hp);
                        //check if opponent lost
                        if($this->check_loss(false)){
                            $this->end_battle(false);
                            return;
                        }
                        //opponent damages player
                        $player_damage = $this->calculate_damage(false, $move_type, $player_defense_boost);
                        echo("Opponent did $player_damage to player!");
                        //deal damage to opponent
                        $this->player_current_hp -= $player_damage;
                        //check if opponent lost
                        if($this->check_loss(true)){
                            $this->end_battle(true);
                            return;
                        }


                    break;
                case ($x <0): //opponent is faster
                    //opponent damages player
                    $player_damage = $this->calculate_damage(false, $move_type, $player_defense_boost);
                    echo("Opponent did $player_damage to player!");
                    //deal damage to opponent
                    $this->player_current_hp -= $player_damage;
                    //check if opponent lost
                    if($this->check_loss(true)){
                        $this->end_battle(true);
                        return;
                        }
                    //Player damages opponent
                    $opponent_damage = $this->calculate_damage(true, $move_type, $opponent_defense_boost);
                    echo("Player did $opponent_damage to opponent!");
                    //deal damage to opponent
                    $this->opponent_current_hp -= $opponent_damage;
                    //check if opponent lost
                    if($this->check_loss(false)){
                        $this->end_battle(false);
                        return;
                    }
                    break;
                default: //there is a tie, flip a coin. TODO make it random
                           //Player damages opponent
                        $opponent_damage = $this->calculate_damage(true, $move_type, $opponent_defense_boost);
                        echo("Player did $opponent_damage to opponent!");
                        //deal damage to opponent
                        $this->opponent_current_hp -= $opponent_damage;
                        //check if opponent lost
                        if($this->check_loss(false)){
                            $this->end_battle(false);
                            return;
                        }
                        //opponent damages player
                        $player_damage = $this->calculate_damage(false, $move_type, $player_defense_boost);
                        echo("Opponent did $player_damage to player!");
                        //deal damage to opponent
                        $this->player_current_hp -= $player_damage;
                        //check if opponent lost
                        if($this->check_loss(true)){
                            $this->end_battle(true);
                            return;
                        }
                    break;
            }
            $this->display_handler->display_table();
        }
    }
    

  ?>