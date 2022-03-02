<?php

namespace hausemaster3734\mFreeze\task;

use hausemaster3734\mFreeze\Loader;
use hausemaster3734\mFreeze\FreezedBase;
use pocketmine\scheduler\Task;
use pocketmine\world\sound\AnvilFallSound;

class FreezeTask extends Task {

    public Loader $plugin;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        foreach(FreezedBase::$freezed as $freezedPlayerName => $time) {
            $player = $this->plugin->getServer()->getPlayerByPrefix($freezedPlayerName);
            if(((int)FreezedBase::$freezed[$freezedPlayerName]%5)==0) {
                $player?->sendTitle(
                    $this->plugin->config->get("frozenTitle"),
                    $this->plugin->config->get("frozenSubTitle"),
                    8, 45, 20
                );
                $player?->getWorld()->addSound($player->getEyePos(), new AnvilFallSound(), [$player]);
            }
            $player?->sendActionBarMessage(str_replace("*time*", FreezedBase::$freezed[$freezedPlayerName], $this->plugin->config->get("frozenActionBar")));
            FreezedBase::$freezed[$freezedPlayerName]--;
            if(FreezedBase::$freezed[$freezedPlayerName]<=0) FreezedBase::onEnd($freezedPlayerName, true);
        }
    }
}