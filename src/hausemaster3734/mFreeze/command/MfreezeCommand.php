<?php

namespace hausemaster3734\mFreeze\command;

use hausemaster3734\mFreeze\Loader;
use hausemaster3734\mFreeze\FreezedBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat;

class MfreezeCommand extends Command implements PluginOwned {

    public Loader $plugin;

    public function __construct(Loader $plugin) {
        parent::__construct("mfreeze", "Freeze a Player", "/mfreeze <player> <message>");
        $this->setPermission("mfreeze.command.use");
        $this->plugin = $plugin;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        $this->testPermission($sender);
        if(count($args) < 2) throw new InvalidCommandSyntaxException();
        $player = $sender->getServer()->getPlayerByPrefix($args[0]);
        $castedSender = $sender->getServer()->getPlayerByPrefix($sender->getName());
        unset($args[0]);
        $message = implode(" ", $args);
        if($player==null) {
            $sender->sendMessage(TextFormat::RED . "Player not found");
            return false;
        }
        FreezedBase::try(
            $player,
            $castedSender,
            $this->plugin->config->get("freezedTime"),
            $message
        );
        return true;
    }

    public function getOwningPlugin(): Plugin {
        return $this->plugin;
    }
}