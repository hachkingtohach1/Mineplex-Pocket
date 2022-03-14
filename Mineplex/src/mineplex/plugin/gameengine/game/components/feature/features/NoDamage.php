<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/8/2015
 * Time: 12:46 AM
 */

namespace mineplex\plugin\gameengine\game\components\feature\features;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Server;

class NoDamage extends ListenerFeature {

    function __construct(Arena $arena)
    {
        parent::__construct($arena);
    }

    function onDamage(EntityDamageEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getEntity()))
            return;

        //Server::getInstance()->broadcastMessage("Stopped!");
        $event->setCancelled();
    }
}