<?php
/**
 * Created by PhpStorm.
 * User: Bench
 * Date: 6/30/2015
 * Time: 9:22 PM
 */
namespace mineplex\plugin\gameengine\arenas\events;

use pocketmine\event\Cancellable;
use pocketmine\Player;
use mineplex\plugin\gameengine\arenas\ArenaEvent;
use mineplex\plugin\gameengine\arenas\Arena;

class ArenaCanJoinEvent extends ArenaEvent implements Cancellable {
    public static $handlerList = null;

    private $player;

    public function __construct(Arena $arena, Player $player)
    {
        parent::__construct($arena);
        $this->player = $player;
    }

    public function getPlayer()
    {
        return $this->player;
    }
}