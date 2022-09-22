<?php
	//These numbers will definitely need to be altered to balance gameplay, hence why they're up here
	CONST rangedattack_defense_boost = 2;
	CONST closeattack_attack_boost = 2;
	CONST defended_defense_boost = 5;


	class Combatant {
		public $name = "name";
		public $species_color = "acara_blue";
		public $level = 1;
		public $attack = 1;
		public $defense = 1;
		public $max_hp = 1;
		
		public $current_hp = 0;
		public $current_attack_type = 'closeattack';
		
		function __construct($name, $species_color, $level, $attack, $defense, $max_hp){
			$this->name = $name;
			$this->species_color = $species_color;
			$this->level = $level;
			$this->attack = $attack;
			$this->defense = $defense;
			$this->max_hp = $max_hp;
			$this->current_hp = $max_hp;
			//TODO handle equipment/weapons. Figure they just increase atk/def
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
				return("<td style='background:grey'>" . $this->current_hp . " / " . $this->max_hp . "</td>");
			if ($this->current_hp/$this->max_hp <= .25)
				return("<td style='background:red'>" . $this->current_hp . " / " . $this->max_hp . "</td>");
			if ($this->current_hp/$this->max_hp <= .50)
				return("<td style='background:orange'>" . $this->current_hp . " / " . $this->max_hp . "</td>");
			return("<td style='background:limegreen'>" . $this->current_hp . " / " . $this->max_hp . "</td>");
		}

	}
	class BattleParty {
		public $petArray = array();
		
		function __construct($pet1, $pet2, $pet3, $pet4){
			$this->petArray[0] = $pet1;
			$this->petArray[1] = $pet2;
			$this->petArray[2] = $pet3;
			$this->petArray[3] = $pet4;
		}
		function check_loss(){
			for($i = 0; $i < 4; $i++){
				if ($this->petArray[$i]->current_hp > 0){
					return false;
				}
			}
			return true;

			}

		function display_images(){
			for($i = 0; $i < 4; $i++){
				echo "<td>" . $this->petArray[$i]->display_pet_img() . "</td>";
			}	
		}
		function display_names(){
			for($i = 0; $i < 4; $i++){
				echo "<td>" . $this->petArray[$i]->name . "</td>";
			}
		}


		function display_hp(){
			for($i = 0; $i < 4; $i++){
				echo $this->petArray[$i]->display_hp();
			}
		}

		//TODO fix this shameful wet code.
		function display_attacks(){
			for($i = 0; $i < 4; $i++){
				if($this->petArray[$i]->current_hp >0)
					echo ("<td><select id='pet" . $i . "_attack'>
						<option value='closeattack'>Close Attack</option>
						<option value='rangedattack'>Ranged Attack</option>
						<option value='defended'>Defend</option>
						</select></td>
						");
				else{
					echo ("<td></td>");
				}
			}
					
		}

		function battle_round(){

		}
	}

	class Opponent extends Combatant{
		//image handling has to change for this
	}

	class OpponentParty{
		//unlike battle party which requires exactly 4 there can be any number of opponents in battle. 
		//Doing it as an array whose length is not determined until execution. maybe linked lists are better but i haven't done one in ages so

		
		public $opponent_list;

		function __construct($opponent_list){
			$this->opponent_list = $opponent_list;
		}

		function get_opponent_names(){
			$opponent_names = array();
			foreach ($this->opponent_list as $opponent){
				if ($opponent->current_hp >0)
					array_push($opponent_names, $opponent->name);

			}
		}
		


	}


//This would be a pull from the database 
	$pet1 = new Combatant("Kori", "draik_darigan", 1, 3, 3, 10);
	$pet2 = new Combatant("Varien", "krawk_royalgirl", 1, 3, 3, 10);
	$pet3 = new Combatant("Luikar", "draik_brown", 1, 3, 3, 0);
	$pet4 = new Combatant("Cazarus", "draik_mutant", 1, 3, 3, 10);

	$opponent1 = new Opponent("Bad Guy", "acara_blue", 1, 1, 1, 1);
	$opponent2 = new Opponent("Bad Guy", "acara_blue", 1, 1, 1, 1);


	$battleparty = new BattleParty($pet1, $pet2, $pet3, $pet4);
	$opponentparty = new OpponentParty([$opponent1, $opponent2]);


//TODO display stuff will all go in its own nice little class
echo ("<table><tr>");
$battleparty->display_images();
echo ("</tr><tr>");
$battleparty->display_names();
echo ("</tr><tr>");
$battleparty->display_hp(); 
echo ("</tr><tr>");
$battleparty->display_attacks();
echo ("</tr><tr>");
$opponent_list = $opponentparty->get_opponent_names();
for($i = 0; $i <4; $i++){
	echo ("
	");

}
?>