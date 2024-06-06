<?php

CONST rangedattack_defense_boost = 2;
CONST closeattack_attack_boost = 2;
CONST defended_defense_boost = 5;

    	class Combatant {
            public $name = "name";
            public $species_color = "acara_blue";
            public $combatant_number = 0;

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
            
            function __construct($combatant_number, $name, $species_color, $level, $attack, $defense, $max_hp, $speed){
                $this->combatant_number = $combatant_number;
                $this->name = $name;
                $this->species_color = $species_color;
                $this->level = $level;
                $this->attack = $attack;
                $this->defense = $defense;
                $this->max_hp = $max_hp;
                $this->current_hp = $max_hp;
                $this->speed = $speed;

                $this->close_attack_img = "https://images.neopets.com/pets/closeattack/" . $this->species_color . "_right.gif";
                $this->ranged_attack_img = "https://images.neopets.com/pets/rangedattack/" . $this->species_color . "_right.gif";
                $this->defended_img = "https://images.neopets.com/pets/defended/" . $this->species_color . "_right.gif";
                $this->downed_img = "https://images.neopets.com/pets/hit/" . $this->species_color . "_right.gif";
                //TODO handle equipment/weapons. Figure they just increase atk/def
            }

            //The set_images class handles where the images for the combatant come from.
            //"host" is neopets or leopets
            //"is_pet" means if it's using pet images or something else. 
            //if something else, then full image URLs need to be provided
            function set_images($host, $is_pet, $direction, $close_attack_img = null, $ranged_attack_img = null, $defended_img = null, $downed_img = null){
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
                    //TODO handle equipment/weapons. Figure they just increase atk/def
                }

            }
    
            //This is future-proofing if we want to add more types of moves later on
            //If any new options are added they must be reflected in calculate_attack_power and take_damage
            function get_moves(){
                
                return (["defended", "closeattack", "rangedattack"]);
            }

            function get_name(){
                return $this->name;
            }

            function calculate_attack_power($attack_type){
                $this->current_attack_type = $attack_type;
                    switch ($attack_type){
                        case "defended":
                            return 0;
                        case "closeattack":
                            return $this->attack * closeattack_attack_boost;
                        case "rangedattack":
                            return ($this->attack);
                            break;	
                    }
            }
        
            function take_damage($incoming_attack_power){
                //TODO implement a better formula that actually takes into consideration the pet's defense stat
    
                switch ($this->current_attack_type){
                    case "defended":
                        $this->current_hp -= $incoming_attack_power/defended_defense_boost;
                        return $this->current_hp;
                    case "closeattack":
                        $this->current_hp -= $incoming_attack_power;
                        return $this->current_hp;
                    case "rangedattack":
                        $this->current_hp -= $incoming_attack_power/rangedattack_defense_boost;
                        return $this->current_hp;
                    default:
                        $this->current_hp -= $incoming_attack_power;
                        return $this->current_hp;
                    }
    
                
            }
            
            function get_image(){
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
    
            function display_hp(){
                if ($this->current_hp <= 0)
                    return("<td style='background:grey'>" . $this->current_hp . " / " . $this->max_hp . "</td> \n");
                if ($this->current_hp/$this->max_hp <= .25)
                    return("<td style='background:red'>" . $this->current_hp . " / " . $this->max_hp . "</td>\n");
                if ($this->current_hp/$this->max_hp <= .50)
                    return("<td style='background:orange'>" . $this->current_hp . " / " . $this->max_hp . "</td>\n");
                return("<td style='background:limegreen'>" . $this->current_hp . " / " . $this->max_hp . "</td>\n");
            }

            function display_attacks(){
                echo ("<td><select name='player_attack'>
                    <option value='closeattack'>Close Attack</option>
                    <option value='rangedattack'>Ranged Attack</option>
                    <option value='defended'>Defend</option>
                    </select></td>
                    ");

            }

            //Future proofing for PVP - will have to check for draws here as well
            function check_loss(){
                return ($this->current_hp <=0);                
            }
        }
  
        class DisplayHandler {

            //TODO give this more flavor text options
            function narrate_attack($attacker, $dmg, $target){
                return "$attacker hit $target for $dmg HP!";
            }
            function narrate_win($winner){
            //TODO flavor text for winning
            echo("Congratulations to $winner!");
            }

            function display_table($player, $opponent){
                                
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
                echo ("<td>" . $player->display_hp() . "</td>"); 
                echo ("<td width=10%></td>");
                echo ("<td>" . $opponent->display_hp() . "</td>");

                echo ("</tr><tr>");
                $player->display_attacks();
                echo ("</tr><tr>");

                echo ("</tr>\n</table> \n<input type='submit' value='Fight!' action='POST'/></form>");
            }
        }

        ?>