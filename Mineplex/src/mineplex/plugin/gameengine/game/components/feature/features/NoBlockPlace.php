<?php
/**
 * Created by PhpStorm.
 * User: TheMineBench
 * Date: 7/1/2015
 * Time: 10:20 PM
 */

namespace mineplex\plugin\gameengine\game\components\feature\features;

use mineplex\plugin\core\commen\ItemContainer;
use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use pocketmine\event\block\BlockPlaceEvent;

class NoBlockPlace extends ListenerFeature {

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
     * @param BlockPlaceEvent $event
     */
    public function onPlace(BlockPlaceEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getPlayer()))
            return;

        if ($this->itemContainer->hasItem($event->getBlock()->getId()))
            $event->setCancelled();
    }
}