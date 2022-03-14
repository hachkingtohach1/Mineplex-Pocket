<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/8/2015
 * Time: 12:42 AM
 */

namespace mineplex\plugin\gameengine\game\components\feature\features;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaJoinEvent;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use mineplex\plugin\gameengine\game\components\spectate\SpectateComponent;
use pocketmine\Server;

class JoinSpectate extends ListenerFeature {

    private $spectateComponent;

    function __construct(Arena $arena, SpectateComponent $spectateComponent)
    {
        parent::__construct($arena);
        $this->spectateComponent = $spectateComponent;
    }

    function onJoin(ArenaJoinEvent $event)
    {
        if ($this->getArena() !== $event->getArena())
            return;
        $this->spectateComponent->enableSpectate($event->getPlayer());
    }
    public function enable()
    {
        parent::enable();
    }

    public function disable()
    {
        parent::disable();
    }

}