<?php


namespace mineplex\plugin\gameengine\game\components\world\event;

use mineplex\plugin\gameengine\arenas\ArenaEvent;
use mineplex\plugin\gameengine\arenas\Arena;
use pocketmine\level\Level;

class WorldLoadSuccessEvent extends ArenaEvent
{
    public static $handlerList = null;

    private $level;

    public function __construct(Arena $arena, Level $level)
    {
        parent::__construct($arena);
        $this->level = $level;
    }

    function getLevel()
    {
        return $this->level;
    }

}