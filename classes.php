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
            
            public $current_hp = 0;
            public $current_attack_type = 'closeattack';

            public $close_attack_img;
            public $ranged_attack_img;
            public $defended_img;
            public $downed_img;
            
            function __construct($combatant_number, $name, $species_color, $level, $attack, $defense, $max_hp){
                $this->combatant_number = $combatant_number;
                $this->name = $name;
                $this->species_color = $species_color;
                $this->level = $level;
                $this->attack = $attack;
                $this->defense = $defense;
                $this->max_hp = $max_hp;
                $this->current_hp = $max_hp;

                $this->closeattack_img = "https://images.neopets.com/pets/closeattack/" . $this->species_color . "_right.gif";
                $this->rangedattack_img = "https://images.neopets.com/pets/rangedattack/" . $this->species_color . "_right.gif";
                $this->defended_img = "https://images.neopets.com/pets/defended/" . $this->species_color . "_right.gif";
                $this->downed_img = "https://images.neopets.com/pets/hit/" . $this->species_color . "_right.gif";
                //TODO handle equipment/weapons. Figure they just increase atk/def
            }

            //for non-neopet opponents. should be called right after construct
            //"host" is neopets or leopets
            //"is_pet" means if it's using pet images or something else. 
            //if something else, then image URLs need to be provided
            function set_images($host, $is_pet, $direction, $closeattack_img = null, $rangedattack_img = null, $defended_img = null, $downed_img = null){
                if(!$is_pet){
                    $this->closeattack_img = $closeattack_img;
                    $this->rangedattack_img = $rangedattack_img;
                    $this->defended_img = $defended_img;
                    $this->downed_img = $downed_img; 
                 }
                else{
                    $this->closeattack_img = $host . "/pets/closeattack/" . $this->species_color . "_$direction.gif";
                    $this->rangedattack_img = $host . "/pets/rangedattack/" . $this->species_color . "_$direction.gif";
                    $this->defended_img = $host .  "/pets/defended/" . $this->species_color . "_$direction.gif";
                    $this->downed_img = $host . "/pets/hit/" . $this->species_color . "_$direction.gif";
                    //TODO handle equipment/weapons. Figure they just increase atk/def
                }

            }
    
            function get_moves(){
                //TODO this is where equipment will be checked for special abilities
                return (["defended", "closeattack", "rangedattack"]);
            }

            function deal_damage($attack_type){
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
                        return $this->closeattack_img;
                    case "rangedattack":
                        return $this->rangedattack_img;
                    case "defended":
                        return $this->defended_img;
                    default:
                        return $this->closeattack_img;
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
    
        }
        class BattleParty {
            public $petArray = array();
            
            function __construct($petArray){
                $this->petArray = $petArray;
            }
            function check_loss(){
                foreach ($this->petArray as $pet){
                    if ($pet->current_hp > 0){
                        return false;
                    }
                }
                return true;
    
                }
    
            function display_images(){
                foreach ($this->petArray as $pet){
                    echo "<td><img src='" . $pet->get_image() . "'></td>";
                }	
            }
            function display_names(){
                foreach ($this->petArray as $pet){
                    echo "<td>" . $pet->name . "</td>";
                }
            }
    
    
            function display_hp(){
                foreach ($this->petArray as $pet){
                    echo $pet->display_hp();
                }
            }
            
            //this only fires for the player's party, never the opponent, so party size will always be 4
            function display_attacks(){
                for($i = 0; $i < 4; $i++){
                    if($this->petArray[$i]->current_hp >0)
                        echo ("<td><select name='pet" . $i . "_attack'>
                            <option value='closeattack'>Close Attack</option>
                            <option value='rangedattack'>Ranged Attack</option>
                            <option value='defended'>Defend</option>
                            </select></td>
                            ");
                    else{
                        echo ("<td><input type='hidden' name='pet" . $i . "_attack' value='0'></td>");
                    }
                }
            }

            function get_valid_targets(){
                $targets =  array(); 
                for($i = 0; $i < count($this->petArray); $i++ ){
                    if ($this->petArray[$i]->current_hp >0)
                        $targets[$i] = $this->petArray[$i]->name ;
                    else{
                        $targets[$i] = 0;
                    }
                }
                return $targets;
            }
    

            function display_targets($targets){
                for($i = 0; $i < 4; $i++){
                    
                    if($this->petArray[$i]->current_hp >0){
                        echo ("<td><select name='pet" . ($i) . "_target'>\n");
                        $targetnumber = 0;
                        foreach ($targets as $targetname){
                            if ($targetname)
                                echo ("<option value='" . $targetnumber . "'>" . $targetname . "</option>\n");
                            $targetnumber++;
                                
                            }
                        }
                    else{
                        echo "<td><input type='hidden' name='pet". $i . "_target' value='0'></td>\n";
                    }
                    }
                }
            }
    
    


        class DisplayHandler {

            //eventually will give this more flavor text options
            function narrate_attack($attacker, $dmg, $target){
                return "$attacker hit $target for $dmg HP!";
            }
            function narrate_win($winner){

            }

            function display_table($battleparty, $opponentparty){
                                
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


                echo ("</tr>\n</table> \n<input type='submit' value='Fight!' action='POST'/></form>");
            }
        }

        ?>