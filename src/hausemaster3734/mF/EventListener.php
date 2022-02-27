<?php

namespace hausemaster3734\mF;

use hausemaster3734\Loader;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityItemPickupEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\inventory\transaction\InventoryTransaction;
use pocketmine\player\Player;

class EventListener implements Listener {

    public Loader $plugin;

    public function __construct(Loader $plugin) {
        $this->plugin = $plugin;
    }

    public function onMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        if(array_key_exists($playerName, FreezedBase::$freezed)) $event->cancel();
    }

    public function onLeave(PlayerQuitEvent $event): void {
        $player = $event->getPlayer();
        $playerName = $player->getName();
        if(array_key_exists($playerName, FreezedBase::$freezed))
            if($this->plugin->config->get("punishmentCommand")!=false) FreezedBase::onEnd($playerName, true);
    }

    public function onDamage(EntityDamageByEntityEvent $event): void {
        if($event->getDamager() instanceof Player and $event->getEntity() instanceof Player) {
            $config = $this->plugin->config;
            $damager = $event->getDamager();
            $entity = $event->getEntity();
            if(array_key_exists($entity->getName(), FreezedBase::$freezed)) {
                $damager->sendMessage($config->get("attackMessage"));
                $event->cancel();
            }
            if(array_key_exists($damager->getName(), FreezedBase::$freezed)) $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event): void {
        if(array_key_exists($event->getPlayer()->getName(), FreezedBase::$freezed)) $event->cancel();
    }

    public function onItemPickup(EntityItemPickupEvent $event): void {
        if($event->getEntity() instanceof Player)
            if(array_key_exists($event->getEntity()->getName(), FreezedBase::$freezed)) $event->cancel();
    }

    public function onItemDrop(InventoryTransactionEvent $event): void {
        if($event->getTransaction() instanceof InventoryTransaction)
            if(array_key_exists($event->getTransaction()->getSource()->getName(), FreezedBase::$freezed)) $event->cancel();
    }

    public function onBlockBreak(BlockBreakEvent $event): void {
        if(array_key_exists($event->getPlayer()->getName(), FreezedBase::$freezed)) $event->cancel();
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        if(array_key_exists($event->getPlayer()->getName(), FreezedBase::$freezed)) $event->cancel();
    }

    public function onCommandPreprocess(PlayerCommandPreprocessEvent $event): void {
        $message = $event->getMessage();
        if(!array_key_exists($event->getPlayer()->getName(), FreezedBase::$freezed)) return;
        if(!(bool)$this->plugin->config->get("allowArrayOfCommands")) {
            $event->cancel();
            return;
        }
        $cmds = $this->plugin->config->get("arrayOfCommands");
        foreach($cmds as $cmd) if(str_contains($message, $cmd)) return;
        $event->cancel();
    }
}
