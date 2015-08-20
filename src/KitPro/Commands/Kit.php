<?php
namespace KitPro\Commands;

use KitPro\KitPro;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class Kit extends BaseCommand{
    /**
     * @param KitPro $plugin
     */
    public function __construct(KitPro $plugin){
        parent::__construct($plugin, "kit", "Choose a kit", "/kit <list|name>");
        $this->setPermission("kitpro.kit");
    }

    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!isset($args[0])){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        switch(strtolower($args[0])){
            case "list":
                $list = $this->getPlugin()->kitList(false);
                $sender->sendMessage($list);
                break;
            default:
                if(!$sender instanceof Player){
                    $sender->sendMessage("[KitPro] Consoles don't need kits!");
                    return false;
                }elseif(!$this->getPlugin()->canGetKit($sender)){
                    $sender->sendMessage("[KitPro] You need to die before you can pick a new kit!");
                    return false;
                }

                $kit = $this->getPlugin()->getKit($args[0]);

                if(!$kit){
                    $sender->sendMessage("[KitPro] Unknown kit named");
                    return false;
                }

                if(!$this->getPlugin()->giveKit($sender, $kit)){
                    $sender->sendMessage("[KitPro] You are not a donator!");
                }else{
                    $this->getPlugin()->addToWaitingList($sender);
                    $sender->sendMessage("[KitPro] Your kit has been given!");
                }
                break;
        }
        return true;
    }
}