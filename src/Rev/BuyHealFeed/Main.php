<?php

namespace Rev\BuyHealFeed;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\Config;

use onebone\economyapi\EconomyAPI;

class Main extends PluginBase implements Listener {
    
    public function onEnable() : void{
        $this->saveDefaultConfig();
        $this->reloadConfig();
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->eco = EconomyAPI::getInstance();
    }
    
    public function onCommand(CommandSender $p, Command $cmd, String $label, array $args): bool
         if ($p instanceof Player) {
             if ($cmd->getName() === 'buyheal') {
                if ($this->eco->myMoney($p) >= $this->getConfig()->get("heal-price")) {
                    $msg = str_replace("{name}", $p->getName(), $this->getConfig()->get("heal-succes-msg"));
                    $this->eco->reduceMoney($p, $this->getConfig()->get("heal-price"));
                    $p->setHealth($p->getMaxHealth());
                    $p->sendMessage($msg);
                } else {
                    $msg = str_replace("{name}", $p->getName(), $this->getConfig()->get("heal-failed-msg"));
                    $p->sendMessage($msg);
                }
             } elseif ($cmd->getName() === 'buyfeed') {
                if ($this->eco->myMoney($p) >= $this->getConfig()->get("feed-price")) {
                    $msg = str_replace("{name}", $p->getName(), $this->getConfig()->get("feed-succes-msg"));
                    $this->eco->reduceMoney($p, $this->getConfig()->get("feed-price"));
                    $p->getHungerManager()->setFood(20);
                    $p->getHungerManager()->setSaturation(20);
                    $p->sendMessage($msg);
                } else {
                    $msg = str_replace("{name}", $p->getName(), $this->getConfig()->get("feed-failed-msg"));
                    $p->sendMessage($msg);
                }
             }
         } else {
             $msg = $this->getConfig()->get("ingame-msg");
             $p->sendMessage($msg);
         }
        return true;
    }
}
