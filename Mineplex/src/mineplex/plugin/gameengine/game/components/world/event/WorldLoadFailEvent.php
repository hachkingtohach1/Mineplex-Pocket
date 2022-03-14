<?php


namespace mineplex\plugin\gameengine\game\components\world\event;

use mineplex\plugin\gameengine\arenas\ArenaEvent;
use mineplex\plugin\gameengine\arenas\Arena;

class WorldLoadFailEvent extends ArenaEvent
{
    public static $handlerList = null;

    public function __construct(Arena $arena)
    {
        parent::__construct($arena);
    }
}