<?php 
namespace KitPro;
use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandExecuter;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\Server;
class KitPro extends PluginBase implements Listener {
	
	public function onEnable(){
		if (!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());
		$this->players = array();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		if(file_exists($this->getDataFolder() . "donators.yml")){
			$this->donators = (new Config($this->getDataFolder()."donators.yml", Config::YAML))->getAll();
		}else{
			$this->donators = array();
		}
		if(file_exists($this->getDataFolder() . "kits.yml")){
			$this -> kits = (new Config($this -> getDataFolder() . "kits.yml", Config::YAML))->getAll();
		}
		else{
			$this->kits = array(
			"soldier" => array(
                "Donator" => false,
                "Items" => array(
                    array(
                        272,
                    	0,
                        1
                    ), // id, meta, count
                    array(
                        260,
                    	0,
                        3
                    ),
                )
            ),
            "wool" => array(
                "Donator" => false,
                "Items" => array(
                    array(
                        35,
                    	0,
                        1
                    ),
                    array(
                        35,
                    	1,
                        1
                    ),
                    array(
                        35,
                    	2,
                        1
                    ),
                )
            ),
            "Donator" => array(
                "Donator" => true,
                "Items" => array(
                    array(
                        276,
                    	0,
                        1
                    ),
                    array(
                        306,
                    	0,
                        1
                    ),
                    array(
                        307,
                    	0,
                        1
                    ),
                )
            ),
			);
		}
		$this->prefix = "[KitPro] ";
	}
	
	public function onDisable(){
		$config = new Config($this->getDataFolder()."donators.yml",Config::YAML,array());
		$config->setAll($this->donators);
		$config->save();
		$kits = new Config($this -> getDataFolder() . "kits.yml", Config::YAML, array());
		$kits->setAll($this->kits);
		$kits->save();
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, $label, array $args){
		if(strtolower($cmd->getName()) === "kit"){
			if(isset($args[0])){
			switch(strtolower($args[0])){
				case "list":
						//$sender->sendMessage($this->prefix . implode(", ", $this->kits));
					$normalKits = 'Normal Kits: ';
					$donatorKits = 'Donator Kits: ';
					foreach ($this->kits as $name => $kit)
					{
						if ($kit['Donator'] == true)
						{
							if ($donatorKits === 'Donator Kits: ')
							{
								$donatorKits .= $name;
							}
							else
							{
								$donatorKits .= ', ' . $name;
							}
						}
						else
						{
							if ($normalKits === 'Normal Kits: ')
							{
								$normalKits .= $name;
							}
							else
							{
								$normalKits .= ', ' . $name;
							}
						}
					}
					if ($normalKits !== 'Normal Kits: ')
					{
						$sender -> sendMessage("[KitPro] " . $normalKits);
					}
					if ($donatorKits !== 'Donator Kits: ')
					{
						$sender -> sendMessage("[KitPro] " . $donatorKits);
					}
						return true;
					break;
				case "":
					if (!$sender instanceof Player)
					{
						$sender->sendMessage("[KitPro] Consoles don't need kits!");
						return true;
					}
					$username = $sender->getName();
					if (in_array($username, $this -> players))
					{
						$sender->sendMessage('[KitPro] You need to die before you can pick a new kit!');
						return true;
					}
					else
					{
						$sender->sendMessage("[KitPro] Usage: /kit <kit name> or /kit list");
						return true;
					}
					break;
				default:
					if (!$sender instanceof Player)
					{
						$sender->sendMessage("[KitPro] Consoles don't need kits!");
						return true;
					}
					$username = $sender->getName();
					if (in_array($username, $this -> players))
					{
						$sender->sendMessage('[KitPro] You need to die before you can pick a new kit!');
						return true;
					}
					if (isset($this -> kits[strtolower($args[0])]))
					{
						$kit = $this -> kits[strtolower($args[0])];
						if ($kit["Donator"] == true and !in_array($username, $this -> donators))
						{
							$sender->sendMessage('You are not a donator!');
							return true;
						}
						else
						{
							$this -> giveKit($kit, $sender);
							$sender->sendMessage('[KitPro] Your kit has been given!');
							array_push($this -> players, $username);
							return true;
						}
					}
					else
					{
						$sender->sendMessage("[KitPro] Usage: /kit <kit name> or /kit list");
						return true;
					}
					break;
			}
			}
			else{
				if (!$sender instanceof Player)
				{
					$sender->sendMessage("[KitPro] Consoles don't need kits!");
					return true;
				}
				$username = $sender->getName();
				if (in_array($username, $this -> players))
				{
					$sender->sendMessage('[KitPro] You need to die before you can pick a new kit!');
					return true;
				}
				else
				{
					$sender->sendMessage("[KitPro] Usage: /kit <kit name> or /kit list");
					return true;
				}
			}
		}
			
			if(strtolower($cmd->getName()) == "donator"){
				if(isset($args[0])){
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
				else{
					$sender->sendMessage("[KitPro] Usage: /donator <add|rmv> <exact username>");
					return true;
				}
			}
		}
		
		public function giveKit($kit, $player)
		{
			foreach ($kit['Items'] as $val)
			{
					$player->getInventory()->addItem(Item::get($val[0],$val[1],$val[2]));
			}
		}
		
		public function onPlayerDeath(PlayerDeathEvent $event){
			$event->getEntity()->sendMessage("[KitPro] You died and may now pick a new kit!");
			$key = array_search($event->getEntity()->getName(),$this->players);
			unset($this->players[$key]);
		}
		
	}
