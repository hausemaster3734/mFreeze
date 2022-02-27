<?php

namespace hausemaster3734;

use hausemaster3734\mF\command\MfreezeCommand;
use hausemaster3734\mF\EventListener;
use hausemaster3734\mF\FreezedBase;
use hausemaster3734\mF\task\FreezeTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase {

    public Config $config;

    public function onEnable(): void  {
        @mkdir($this->getDataFolder());
        $this->getLogger()->notice("mFreeze says: \"I was successfully loaded\".");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        $this->config = $this->getConfig();
        $this->getScheduler()->scheduleRepeatingTask(new FreezeTask($this), 20);
        $this->getServer()->getCommandMap()->register("mfreeze", new MfreezeCommand($this));
        new FreezedBase($this);
        $this->saveDefaultConfig();
    }

    public function onDisable(): void {
        $this->getLogger()->notice("mFreeze says: \"I was successfully disabled\".");
        $this->saveDefaultConfig();
    }
}
