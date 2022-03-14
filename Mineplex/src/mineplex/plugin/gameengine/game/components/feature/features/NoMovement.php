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
use pocketmine\event\player\PlayerMoveEvent;

class NoMovement extends ListenerFeature {

    public function __construct(Arena $arena)
    {
        parent::__construct($arena);
    }

    /**
     * @ignoreCancelled true
     * @param PlayerMoveEvent $event
     */
    public function onMove(PlayerMoveEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getPlayer()))
            return;
        if (($event->getFrom()->getX() == $event->getTo()->getX()) && ($event->getFrom()->getY() == $event->getTo()->getY()) && ($event->getFrom()->getZ() == $event->getTo()->getZ()))
            return;
        $event->setCancelled();
    }

}