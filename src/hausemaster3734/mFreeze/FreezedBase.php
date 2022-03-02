<?php

namespace hausemaster3734\mFreeze;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\player\Player;

class FreezedBase {

    public static array $freezed = [];

    public static Loader $plugin;

    public function __construct(Loader $plugin) {
        self::$plugin = $plugin;
    }

    public static function addFreezed(Player $player, Player $whoFreezed, int $time, string $message): void {
        $whoFreezedName = $whoFreezed->getName();
        $playerName = $player->getName();
        $config = self::$plugin->config;
        self::$freezed[] = $playerName;
        self::$freezed[$playerName] = $time;
        $player->sendMessage(str_replace(["*sender*", "*msg*"], [$whoFreezedName, $message], $config->get("frozenMsg")));
        $whoFreezed->sendMessage(str_replace(["*player*", "*time*", "*msg*"], [$playerName, $time, $message], $config->get("senderMsg")));
    }

    public static function delFreezed(string $playerName): void {
        if(array_key_exists($playerName, self::$freezed)) unset(self::$freezed[$playerName]);
    }

    public static function onEnd(string $playerName, bool $punish = false): void {
        unset(self::$freezed[$playerName]);
        if($punish===true) {
            $commandExecutor = new ConsoleCommandSender(self::$plugin->getServer(), self::$plugin->getServer()->getLanguage());
            $command = self::$plugin->config->get("punishmentCommand");
            self::$plugin->getServer()->dispatchCommand($commandExecutor, $command);
        }
        $player = self::$plugin->getServer()->getPlayerByPrefix($playerName);
        $player?->sendMessage(self::$plugin->config->get("unfreezeMessage"));
    }

    public static function try(Player $player, Player $whoFreezed, int $time, string $message): void {
        if(array_key_exists($player->getName(), self::$freezed)) self::onEnd($player->getName());
            else self::addFreezed($player, $whoFreezed, $time, $message);
    }
}
