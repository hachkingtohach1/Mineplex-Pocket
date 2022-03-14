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
use mineplex\plugin\core\commen\ItemContainer;use pocketmine\event\player\PlayerDropItemEvent;

class NoDropItem extends ListenerFeature {

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
     * @param PlayerDropItemEvent $event
     */
    public function onDrop(PlayerDropItemEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getPlayer()))
            return;

        if ($this->itemContainer->hasItem($event->getItem()->getId()))
            $event->setCancelled();
    }
}