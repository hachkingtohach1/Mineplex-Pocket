<?php
/**
 * Created by PhpStorm.
 * User: Exerosis
 * Date: 7/12/2015
 * Time: 9:25 PM
 */

namespace mineplex\plugin\gameengine\game\components\world;


use mineplex\plugin\gameengine\arenas\Arena;
use mineplex\plugin\gameengine\game\components\world\event\WorldLoadSuccessEvent;

//This is a simple fix for Chiss' allergies to Night time
class TimeComponent {

    private $arena;
    private $worldComponent;
    private $time;

    /**
     * @param Arena $arena
     * @param WorldComponent $worldComponent
     * @param int $time
     */
    function __construct(Arena $arena, WorldComponent $worldComponent, $time = 6000)
    {
        $this->arena = $arena;
        $this->worldComponent = $worldComponent;
        $this->time = $time;
    }

    public function onWorld(WorldLoadSuccessEvent $event)
    {
        if ($event->getArena() !== $this->arena)
            return;
        $event->getLevel()->setTime($this->time);
        $event->getLevel()->stopTime();
    }

}