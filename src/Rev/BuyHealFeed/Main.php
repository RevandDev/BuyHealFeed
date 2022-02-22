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
use cooldogedev\BedrockEconomy;

class Main extends PluginBase implements Listener {
    
    public function onEnable() : void{
        $this->saveDefaultConfig();
        $this->reloadConfig();
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");       
    }
    
    public function onCommand(CommandSender $p, Command $cmd, String $label, array $args): bool{
         if ($p instanceof Player) {
             if ($cmd->getName() === 'buyheal') {
                if ($this->myMoney($p) >= $this->getConfig()->get("heal-price")) {
                    $msg = str_replace("{name}", $p->getName(), $this->getConfig()->get("heal-succes-msg"));
                    $this->reduceMoney($p, $this->getConfig()->get("heal-price"));
                    $p->setHealth($p->getMaxHealth());
                    $p->sendMessage($msg);
                } else {
                    $msg = str_replace("{name}", $p->getName(), $this->getConfig()->get("heal-failed-msg"));
                    $p->sendMessage($msg);
                }
             } elseif ($cmd->getName() === 'buyfeed') {
                if ($this->myMoney($p) >= $this->getConfig()->get("feed-price")) {
                    $msg = str_replace("{name}", $p->getName(), $this->getConfig()->get("feed-succes-msg"));
                    $this->reduceMoney($p, $this->getConfig()->get("feed-price"));
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
    
    public function myMoney($player)
    {
        $pn = $player->getName();
        BedrockEconomy::getAPI()->getPlayerBalance($pn);
    }
    
    public function reduceMoney($player, $int)
    {
        $pn = $player->getName();
        $bal = BedrockEconomy::getAPI()->getPlayerBalance($pn);
        BedrockEconomy::getAPI()->setPlayerBalance($pn, $bal - $int);
    }
}
