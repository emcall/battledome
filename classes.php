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
            
            function __construct($combatant_number, $name, $species_color, $level, $attack, $defense, $max_hp){
                $this->combatant_number = $combatant_number;
                $this->name = $name;
                $this->species_color = $species_color;
                $this->level = $level;
                $this->attack = $attack;
                $this->defense = $defense;
                $this->max_hp = $max_hp;
                $this->current_hp = $max_hp;
                //TODO handle equipment/weapons. Figure they just increase atk/def
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
                        break;
                    }
    
                
            }
            
            //TODO have to keep in mind neo-hosted vs leo-hosted, i assume there's a flag in the db
            function display_pet_img(){
                if ($this->current_hp <= 0)
                    return "<img src='https://images.neopets.com/pets/hit/" . $this->species_color . "_right.gif'>";
                return "<img src='https://images.neopets.com/pets/" . $this->current_attack_type . "/" . $this->species_color . "_right.gif'>";
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
                    echo "<td>" . $pet->display_pet_img() . "</td>";
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
    
    
        class Opponent extends Combatant{
            //image handling has to change for this for a couple reasons:
            //if the image is of a pet then switch right to left 
            //if the image is something else then we need an entirely new url
            function display_pet_img(){
                if ($this->current_hp <= 0)
                    return "<img src='https://images.neopets.com/pets/hit/" . $this->species_color . "_left.gif'>";
                return "<img src='https://images.neopets.com/pets/" . $this->current_attack_type . "/" . $this->species_color . "_left.gif'>";
            }

            
        }

        class DisplayHandler {

            function narrate_attack($attacker, $dmg, $target){
                return "$attacker hit $target for $dmg";
            }

        }

        ?>