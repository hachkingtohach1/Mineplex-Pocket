<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/6/2015
 * Time: 2:38 AM
 */

namespace mineplex\plugin\gameengine\game\components\feature\features;

use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use mineplex\plugin\gameengine\game\components\spectate\SpectateComponent;
use pocketmine\event\player\PlayerDeathEvent;

class DeathSpectate extends ListenerFeature {

    private $spectateComponent;

    function __construct(Arena $arena, SpectateComponent $spectateComponent)
    {
        parent::__construct($arena);
        $this->spectateComponent = $spectateComponent;
    }

    /**
     * @ignoreCancelled true
     * @param PlayerDeathEvent $event
     */
    function onDeath(PlayerDeathEvent $event)
    {
        if (!$this->getArena()->hasPlayer($event->getEntity()))
            return;

        if ($this->spectateComponent->enableSpectate($event->getEntity()))
        {
            //Do death stuff
            $event->getEntity()->sendTip('§4§lYOU DIED!');
        }
    }
}