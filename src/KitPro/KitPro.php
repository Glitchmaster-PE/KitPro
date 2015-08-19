<?php 
namespace KitPro;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandExecuter;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\Config;

class KitPro extends PluginBase {
	
	public function onEnable(){
		$this->players = array();
		if(file_exists($this->getDataFolder() . "donators.yml")){
			$this->donators = (new Config($this->getDataFolder()."donators.yml", Config::YAML))->getAll();
		}else{
			$this->donators = array();
		}
		$this->prefix = "[KitPro]";
	}
	
	public function onDisable(){
		$config = new Config($this->getDataFolder()."donators.yml",Config::YAML,array());
		$config->setAll($this->donators);
		$config->save();
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		if(strtolower($cmd->getName()) === "kitpro"){
			switch(strtolower($args[0])){
				case "list":
					if(in_array($sender->getName(), $this->donators)){
						$sender->sendMessage($this->prefix . "Kits: Defense, Offense, Survival, Donator");
						return true;
					}
					else{
						$sender->sendMessage($this->prefix . "Kits: Defense, Offense, Survival");
						return true;
					}
					break;
				case "defense":
					if($sender instanceof Player){
						if(in_array($sender->getName(), $this->players)){
							$sender->sendMessage($this->prefix . "You already have a kit!");
						}
						else{
							$sender->sendMessage($this->prefix . "You have recieved the Defense Kit!");
							$sender->getInventory()->addItem(Item::get(Item::IRON_HELMET));
							$sender->getInventory()->addItem(Item::get(Item::IRON_BOOTS));
							$sender->getInventory()->addItem(Item::get(Item::IRON_LEGGINGS));
							$sender->getInventory()->addItem(Item::get(Item::IRON_CHESTPLATE));
							$sender->getInventory()->addItem(Item::get(Item::STONE_SWORD));
							array_push($this->players, $sender->getName());
						}
						return true;
					}
					else{
						$sender->sendMessage($this->prefix . "Consoles don't need kits!");
						return true;
					}
					break;
				case "offense":
					if($sender instanceof Player){
						if(in_array($sender->getName(), $this->players)){
							$sender->sendMessage($this->prefix . "You already have a kit!");
						}
						else{
							$sender->sendMessage($this->prefix . "You have recieved the Offense Kit!");
							$sender->getInventory()->addItem(Item::get(Item::DIAMOND_SWORD));
							$sender->getInventory()->addItem(Item::get(Item::LEATHER_CAP));
							$sender->getInventory()->addItem(Item::get(Item::LEATHER_BOOTS));
							$sender->getInventory()->addItem(Item::get(Item::LEATHER_PANTS));
							$sender->getInventory()->addItem(Item::get(Item::LEATHER_TUNIC));
							array_push($this->players, $sender->getName());
						}
						return true;
					}
					else{
						$sender->sendMessage($this->prefix . "Consoles don't need kits!");
						return true;
					}
					break;
				case "survival":
					if($sender instanceof Player){
						if(in_array($sender->getName(), $this->players)){
							$sender->sendMessage($this->prefix . "You already have a kit!");
						}
						else{
							$sender->sendMessage($this->prefix . "You have recieved the Survival Kit!");
							$sender->getInventory()->addItem(Item::get(Item::IRON_PICKAXE));
							$sender->getInventory()->addItem(Item::get(Item::IRON_AXE));
							$sender->getInventory()->addItem(Item::get(Item::IRON_SHOVEL));
							$sender->getInventory()->addItem(Item::get(Item::IRON_SWORD));
							for($i = 1; $i <= 20; $i++){
								$sender->getInventory()->addItem(Item::get(Item::STEAK));
							}
							array_push($this->players, $sender->getName());
						}
						return true;
					}
					else{
						$sender->sendMessage($this->prefix . "Consoles don't need kits!");
						return true;
					}
					break;
				case "donator":
					if($sender instanceof Player){
						if(in_array($sender->getName(), $this->players)){
							$sender->sendMessage($this->prefix . "You already have a kit!");
						}
						else{
							if(in_array($sender->getName(), $this->donators)){
								$sender->sendMessage($this->prefix . "You have recieved the Donator Kit!");
								$sender->getInventory()->addItem(Item::get(Item::DIAMOND_SWORD));
								$sender->getInventory()->addItem(Item::get(Item::IRON_HELMET));
								$sender->getInventory()->addItem(Item::get(Item::IRON_BOOTS));
								$sender->getInventory()->addItem(Item::get(Item::IRON_LEGGINGS));
								$sender->getInventory()->addItem(Item::get(Item::IRON_CHESTPLATE));
								array_push($this->players, $sender->getName());
							}
							else{
								$sender->sendMessage($this->prefix . "You are not a donator!");
							}
						}
						return true;
						}	
					else{
						$sender->sendMessage($this->prefix . "Consoles don't need kits!");
						return true;
					}
				break;
				case "reset":
					if(in_array($sender->getName(), $this->players)){
						$sender->sendMessage($this->prefix . "Do you really want to reset? This will clear your whole inventory. If so, do /kit resetyes");
						return true;
					}
					
					else{
						$sender->sendMessage($this->prefix . "You can't reset, you haven't even selected a kit!");	
						return true;
					}
					
					break;
				case "resetyes":
					if(in_array($sender->getName(), $this->players)){
						$sender->sendMessage($this->prefix . "You're inventory has been reset, you may pick a new kit!");
						$sender->getInventory()->clearAll();
						$key = array_search($sender->getName(), $this -> players);
						unset($this -> players[$key]);
						return true;
					}
					else{
						$sender->sendMessage($this->prefix . "You can't reset, you haven't even selected a kit!");
						return true;
					}
					break;
				default:
					if(in_array($sender->getName(), $this->players)){
						$sender->sendMessage($this->prefix . "Usage: /kitpro reset");
						return true;
					}
					else{
						$sender->sendMessage($this->prefix . "Usage: /kitpro <kit name> or /kitpro list");
						return true;
					}
					break;
				}
			}
			
			if(strtolower($cmd->getName()) === "donator"){
				switch(strtolower($args[0])){
					case "add":
						$sender->sendMessage($this->prefix . "" . $args[1] . " has been added as a donator!");
						array_push($this->donators, $args[1]);
						return true;
						break;
					case "rmv":
						$sender->sendMessage($this->prefix . "" . $args[1] . " has been removed as a donator!");
						$key = array_search($args[1], $this -> donators);
						unset($this -> donators[$key]);
						return true;
						break;
					default:
						$sender->sendMessage($this->prefix . "Usage: /donator <add|rmv> <exact username>");
						return true;
						break;
				}
			}
		}
	}
