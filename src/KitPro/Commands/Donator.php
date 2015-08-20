<?php
namespace KitPro\Commands;

use KitPro\KitPro;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Donator extends BaseCommand{
    public function __construct(KitPro $plugin){
        parent::__construct($plugin, "donator", "Add or Remove a user from donators list!", "/donator <add|remove> <exact username>");
        $this->setPermission("kitpro.donator");
    }

    public function execute(CommandSender $sender, $alias, array $args){
        if(!$this->testPermission($sender)){
            return false;
        }
        if(!isset($args[0])){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }elseif(!isset($args[1])){
            $sender->sendMessage(TextFormat::RED . $this->getUsage());
            return false;
        }
        switch($args[0]){
            case "add":
                $this->getPlugin()->addDonator($args[1]);
                $sender->sendMessage("[KitPro] Added " . $args[1] . " to the donators list!");
                break;
            case "remove":
                $this->getPlugin()->removeDonator($args[1]);
                $sender->sendMessage("[KitPro] Removed " . $args[1] . " from the donators list!");
                break;
            default:
                $sender->sendMessage(TextFormat::RED . $this->getUsage());
                return false;
                break;
        }
        return true;
    }
}