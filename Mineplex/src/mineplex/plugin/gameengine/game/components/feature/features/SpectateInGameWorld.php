<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/12/2015
 * Time: 12:41 AM
 */
namespace mineplex\plugin\gameengine\game\components\feature\features;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\arenas\events\ArenaJoinEvent;
use mineplex\plugin\gameengine\game\components\feature\ListenerFeature;
use mineplex\plugin\gameengine\game\components\spectate\events\EnableSpectateEvent;
use mineplex\plugin\gameengine\game\components\spectate\SpectateComponent;
use mineplex\plugin\gameengine\game\components\world\WorldComponent;
use mineplex\plugin\util\UtilTeleport;
use pocketmine\block\Thin;

class SpectateInGameWorld extends ListenerFeature {

    private $worldComponent;

    /**
     * @param Arena $arena
     * @param WorldComponent $worldComponent
     * @param SpectateComponent $spectateComponent just here to make sure that there is a SpectateComponent
     */
    function __construct(Arena $arena, WorldComponent $worldComponent, SpectateComponent $spectateComponent)
    {
        parent::__construct($arena);
        $this->worldComponent = $worldComponent;

    }

    function onJoin(EnableSpectateEvent $event)
    {
        if ($this->getArena() !== $event->getArena() || !$this->worldComponent->isWorldReady())
            return;

        if ($event->getPlayer()->getPosition()->getLevel() == $this->worldComponent->getWorld())
            return;

        UtilTeleport::teleport($event->getPlayer(), $this->worldComponent->getWorld()->getSpawnLocation());
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