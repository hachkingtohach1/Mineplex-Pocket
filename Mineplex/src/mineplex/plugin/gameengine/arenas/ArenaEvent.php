<?php
/**
 * Created by PhpStorm.
 * User: Bench
 * Date: 6/30/2015
 * Time: 9:06 PM
 */
namespace mineplex\plugin\gameengine\arenas;

use pocketmine\event\Event;

class ArenaEvent extends Event
{
    private $arena;

    public function __construct(Arena $arena)
    {
        $this->arena = $arena;
    }

    public function getArena()
    {
        return $this->arena;
    }
}