<?php

namespace hausemaster3734\mFreeze;

use hausemaster3734\mFreeze\command\MfreezeCommand;
use hausemaster3734\mFreeze\task\FreezeTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase {

    public Config $config;

    public function onEnable(): void  {
        @mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->config = $this->getConfig();
        $this->getScheduler()->scheduleRepeatingTask(new FreezeTask($this), 20);
        $this->getServer()->getCommandMap()->register("mfreeze", new MfreezeCommand($this));
        new FreezedBase($this);
        $this->saveDefaultConfig();
    }

    public function onDisable(): void {
        $this->saveDefaultConfig();
    }
}
