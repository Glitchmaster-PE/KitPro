<?php
namespace KitPro\Commands;

use KitPro\KitPro;
use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;

abstract class BaseCommand extends Command implements PluginIdentifiableCommand{
    /** @var KitPro */
    private $plugin;

    /**
     * @param KitPro $plugin
     * @param string $name
     * @param string $description
     * @param null $usageMessage
     * @param array $aliases
     */
    public function __construct(KitPro $plugin, $name, $description = "", $usageMessage = null, array $aliases = []){
        parent::__construct($name, $description, $usageMessage, $aliases);
        $this->plugin = $plugin;
    }

    /**
     * @return KitPro
     */
    public function getPlugin(){
        return $this->plugin;
    }
}