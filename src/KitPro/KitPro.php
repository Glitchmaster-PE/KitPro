<?php 
namespace KitPro;

use KitPro\Commands\Donator;
use KitPro\Commands\Kit;
use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;

class KitPro extends PluginBase implements Listener{
	/** @var Config */
	private $kits = [];
	/** @var Config */
	private $donators = [];
    /** @var array */
    private $players = [];
	
	public function onEnable(){
		if(!is_dir($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}
		$this->players = array();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->registerAll("KitPro", [
            new Kit($this), new Donator($this)
        ]);
		if(file_exists($this->getDataFolder() . "donators.yml")){
			$this->donators = new Config($this->getDataFolder()."donators.yml", Config::YAML);
		}
        $this->kits = new Config($this -> getDataFolder() . "kits.yml", Config::YAML, [
            "soldier" => [
                "Donator" => false,
                "Items" => [
                    [272, 0, 1],
                    [260, 0, 3]
                ]
            ],
            "wool" => [
                "Donator" => false,
                "Items" => [
                    [35, 0, 1],
                    [35, 1, 1],
                    [35, 2, 1]
                ]
            ],
            "donator" => [
                "Donator" => true,
                "Items" => [
                    [276, 0, 1],
                    [306, 0, 1],
                    [307, 0, 1]
                ]
            ]
        ]);
	}
	
	public function onDisable(){
		$this->donators->save();
		$this->kits->save();
	}

    /**
     * @param PlayerDeathEvent $event
     *
     * @ignoreCancelled true
     * @priority MONITOR
     */
    public function onPlayerDeath(PlayerDeathEvent $event){
        $this->players[strtolower($event->getEntity()->getName())] = false;
		$event->getEntity()->sendMessage("[KitPro] You died and may now pick a new kit!");
    }

    /**
     * @param string $name
     * @return array|bool
     */
	public function getKit($name){
		$name = strtolower($name);
        return $this->kits->exists($name) ? $this->kits->get($name) : false;
	}

    /**
     * @param bool $inArray
     * @return array|string
     */
	public function kitList($inArray = false){
		$kits = [
			"Normal kits" => [],
			"Donator kits" => []
		];
		foreach($this->kits->getAll() as $key => $values){
			if($values["Donator"]){
				$kits["Donator kits"][] = $key;
			}else{
				$kits["Normal kits"][] = $key;
			}
		}
		if(!$inArray){
			$k = "[KitPro] Available kits:\n - Normal kits:";
			$d = "\n - Donator kits:";
			foreach($kits as $type => $list){
				if($type === "Normal kits"){
					$k.= implode("\n  * ", $list);
				}else{
					$d.= implode("\n  * ", $list);
				}
			}
			$kits = $k . $d;
		}
		return $kits;
	}

    /**
     * @param Player $player
     * @param array $kit
     * @return bool
     */
    public function giveKit(Player $player, array $kit){
        if($kit["Donator"] && !$this->isDonator($player)){
            return false;
        }
        $items = $kit["Items"];
        foreach(($items) as $key => $values){
            if(!isset($values[1])){
                $values[1] = 0;
            }
            if(!isset($values[2])){
                $values[2] = 1;
            }
            $items[$key] = new Item($values[0], $values[1], $values[2]);
        }
        $player->getInventory()->addItem(...$items);
        return true;
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function isDonator(Player $player){
        return $this->donators->exists($player->getName(), true);
    }

    /**
     * @param string $name
     */
    public function addDonator($name){
        $this->donators->set(strtolower($name), true);
    }

    /**
     * @param string $name
     */
    public function removeDonator($name){
        if($this->donators->exists($name, true)){
            $this->donators->remove(strtolower($name));
        }
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function canGetKit(Player $player){
        return (!isset($this->players[strtolower($player->getName())]) || !$this->players[strtolower($player->getName())]);
    }

    /**
     * @param Player $player
     */
    public function addToWaitingList(Player $player){
        $this->players[strtolower($player->getName())] = true;
    }
}
