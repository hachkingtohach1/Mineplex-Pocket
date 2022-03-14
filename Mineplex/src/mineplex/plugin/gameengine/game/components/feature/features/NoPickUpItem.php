<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/1/2015
 * Time: 10:20 PM
 */

namespace mineplex\plugin\gameengine\game\components\feature\features;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use mineplex\plugin\core\commen\ItemContainer;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\item\Item;
use pocketmine\Server;

class NoPickUpItem extends ListenerFeature {

    private $itemContainer;

    /**
     * @param Arena $arena
     * @param int[] $ids
     * @param bool $black
     */
    public function __construct(Arena $arena, array $ids = null, $black = false)
    {
        parent::__construct($arena, $ids, $black);
        $this->itemContainer = new ItemContainer($ids, $black);
    }
    /**
     * @ignoreCancelled true
     * @param InventoryPickupItemEvent $event
     */
    public function onPickUp(InventoryPickupItemEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getInventory()->getHolder())) {
            Server::getInstance()->broadcastMessage("You're not in the arena!");
            return;
        }

        Server::getInstance()->broadcastMessage("Grass: " . Item::GRASS);
        Server::getInstance()->broadcastMessage("Item: " . $event->getItem()->getItem()->getId());

        if ($this->itemContainer->hasItem($event->getItem()->getItem()->getId())) {
            $event->setCancelled();
            Server::getInstance()->broadcastMessage("Stopped!");
        } else
            Server::getInstance()->broadcastMessage("Not Stopped!");


    }


}