<?php
	//These numbers will definitely need to be altered to balance gameplay, hence why they're up here
	CONST rangedattack_defense_boost = 2;
	CONST closeattack_attack_boost = 2;
	CONST defended_defense_boost = 5;
	session_start();



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

		function display_attacks(){
			for($i = 0; $i < 4; $i++){
				if($this->petArray[$i]->current_hp >0)
					echo ("<td><select name='pet" . ($i+1) . "_attack'>
						<option value='closeattack'>Close Attack</option>
						<option value='rangedattack'>Ranged Attack</option>
						<option value='defended'>Defend</option>
						</select></td>
						");
				else{
					echo ("<td><input type='hidden' name='pet" . ($i+1) . "_attack' value='0'></td>");
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

	class OpponentParty extends BattleParty{

		function get_valid_targets(){
			$targets = ""; 
			foreach ($this->petArray as $opponent){
				if ($opponent->current_hp >0)
					$targets .= "<option value='" . $opponent->name . "'>" . $opponent->name . "</option> \n";
			}
			return $targets;
		}

	}

	function battle_round(){
		

	}


//This would be a pull from the database/session data

	$pet1 = new Combatant("Kori", "draik_darigan", 1, 3, 3, 10);
	$pet2 = new Combatant("Varien", "krawk_royalgirl", 1, 3, 3, 10);
	$pet3 = new Combatant("Luikar", "draik_brown", 1, 3, 3, 0);
	$pet4 = new Combatant("Cazarus", "draik_mutant", 1, 3, 3, 10);

	$opponent1 = new Opponent("Bad Guy1", "acara_blue", 1, 1, 1, 1);
	$opponent2 = new Opponent("Bad Guy2", "aisha_blue", 1, 1, 1, 1);


	$battleparty = new BattleParty([$pet1, $pet2, $pet3, $pet4]);
	$opponentparty = new OpponentParty([$opponent1, $opponent2]);

$_SESSION["battleparty"] = $battleparty;
$_SESSION["opponentparty"] = $battleparty;

//TODO display stuff will all go in its own nice little class, split out from the combatant/party classes. 
// this is easier for quick testing, i usually do display classes last

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
//TODO this shouldn't display for KO'd party members
$targets = $opponentparty->get_valid_targets();
for($i = 0; $i <4; $i++){
	echo ("<td><select id='pet" . ($i + 1) . "_target'>");
	echo $targets . "</td>";
}

echo ("</tr>\n</table> \n<input type='submit' action='POST'/></form>");
?>