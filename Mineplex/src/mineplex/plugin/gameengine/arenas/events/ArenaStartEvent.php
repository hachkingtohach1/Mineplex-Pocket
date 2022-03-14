<?php
/**
 * Created by PhpStorm.
 * User: Bench
 * Date: 6/30/2015
 * Time: 10:12 PM
 */
namespace mineplex\plugin\gameengine\arenas\events;

use mineplex\plugin\gameengine\arenas\ArenaEvent;
use mineplex\plugin\gameengine\arenas\Arena;

class ArenaStartEvent extends ArenaEvent{
    public static $handlerList = null;

    public function __construct(Arena $arena)
    {
        parent::__construct($arena);
    }
}